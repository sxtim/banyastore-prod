<?php

namespace App\Services\Basket;


use App\Models\Category;
use App\Models\Discount\Discount;
use App\Models\Order;
use App\Models\PaymentCertificate\PaymentCertificate;
use App\Models\Product;
use App\Models\Promocode;
use App\Models\PromocodeMode;
use App\Models\Purveyor;
use App\Models\SessionBasket;
use App\Models\SessionBasketProduct;
use App\Services\CategoryService;
use App\Services\DiscountService;
use App\Services\PaymentCertificates\PaymentCertificateService;
use App\Services\Promocode\PromocodeService;
use App\Services\Shop\Product\ProductService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SessionBasketService implements BasketInterface
{
    private $price;
    private CategoryService $categoryService;
    private ?SessionBasket $userBasket;

    //мегакостыль
    public function __construct(
        CategoryService $categoryService,
        private readonly PaymentCertificateService $paymentCertificateService,
        private readonly ProductService $productService,
        private readonly PromocodeService $promocodeService
    )
    {
        $this->categoryService = $categoryService;
        $this->userBasket = SessionBasket::where('session_uid','=', \Session::get(SessionBasket::SESSION_UID_NAME))->first();
    }

    private function createSessionUidCart(): string
    {
        $sessionUid = Str::random(40);
        \Session::put(SessionBasket::SESSION_UID_NAME, $sessionUid);

        return $sessionUid;
    }

    public function getBasket(bool $isFull = false): Collection
    {
        $transformedCart = collect();

        if ($this->userBasket === null) {
            return $transformedCart;
        }

        // TODO вытащить категории не создавая новых запросов
        $categories = Category::select(['id', 'parent_id', 'name'])->get();

        $discountService = new DiscountService();

        if ($isFull) {
            $userBasket =  $this->userBasket
                ->products()
                ->with(['product.category', 'product.allProperties', 'product.purveyor','product.stocks','product.stocks.regions']);
        } else {
            $userBasket = $this->userBasket
                ->products()
                ->where('is_check','=', true)
                ->with(['product.category', 'product.allProperties', 'product.purveyor','product.stocks','product.stocks.regions']);
        }

        $userBasket
            ->each(function (SessionBasketProduct $basketProduct) use ($transformedCart, $categories) {
                $basketItem = new BasketItem();

                $model = $basketProduct->product;

                $quantityProduct = $this->productService->getQuantity($model);

                if (!$model || $quantityProduct <= 0) {
                    $basketProduct->delete();
                    return true;
                }
                if ($quantityProduct >= $basketProduct->quantity) {
                    $quantity = $basketProduct->quantity;
                } else {
                    $basketProduct->update([
                        'quantity' => $quantityProduct
                    ]);
                    $quantity = $quantityProduct;
                }

                $basketItem->stockId = $this->productService->getStockId($model);
                $basketItem->basket_product_id = $basketProduct->id;
                $basketItem->basket_id = $basketProduct->basket_id;
                $basketItem->maxQuantity = $quantityProduct;
                $basketItem->isCheck = $basketProduct->is_check;
                $basketItem->product = $model->only(['id', 'name', 'image', 'classifier']);
                $basketItem->product['link'] = route('shop.product', ['product' => $model->urlHash(),'slug' => $model['slug']]);
                $basketItem->quantity = $quantity;
                $basketItem->price = $model->price;
                $basketItem->promoPrice = $model->promo_price > 0 && $model->promo_price < $model->price ? $model->promo_price : $model->price;
                $basketItem->basePrice = $model->base_price;

                $basketItem->subtotal = $model->price * $basketProduct->quantity;

                $basketItem->discount = 0;
                $basketItem->promocodeDiscountPerPiece = 0;
                $basketItem->certificateDiscountPerPiece = 0;
                $basketItem->cashbackDiscountPerPiece = 0;
                $basketItem->priceDiscountPerPiece = 0;
                $basketItem->isAd = $model->is_ad;
                $basketItem->start_price = $model->price;
                $basketItem->purveyor = $model->purveyor->only(['id', 'inn', 'name','name_sait']);
                $basketItem->category = $model->category->name;
                $basketItem->category_id = $model->category->id;
                $basketItem->rootCategory = $this->categoryService->getRootCategory($categories, $model->category->id)->name;
                $basketItem->dimensions = [
                    'weight' => $model->getWeight(),
                    'length' => $model->getLength(),
                    'height' => $model->getHeight(),
                    'width' => $model->getWidth(),
                ];

                $transformedCart->push($basketItem);
            });

        //Если применен промокод
        if ($this->userBasket->promocode) {
            $transformedCart = $this->promocodeService->distributeFixDiscount($transformedCart, $this->userBasket->promocode);
        }

        //Если применен сертификат
        if ($this->userBasket->paymentCertificate) {
            $transformedCart = $this->paymentCertificateService->apply($transformedCart, $this->userBasket->paymentCertificate);
        }

        //Скидки
        $discount = $discountService->getDiscount($transformedCart);
        if ($discount && $discount !== $this->getDiscount()) {
            $this->addDiscount($discount);
        }
        if ($discount === null && $this->getDiscount() !== null) {
            $this->deleteDiscount();
        }
        if ($discount) {
            $transformedCart = $discountService->applyDiscount($transformedCart, $discount);
        }

        return $transformedCart->sortBy('product.name');
    }

    public function getTotalDiscount(): float
    {
        return $this->userBasket->products->sum(function (SessionBasketProduct $product) {
            return $product->product->price - $product->product[$this->price];
        });
    }

    public function getTotal(): float
    {
        return $this->userBasket->products->sum(function (SessionBasketProduct $product) {
            return $product->product[$this->price];
        });
    }

    public function getCount(): int
    {
        return $this->userBasket->products->sum(function (SessionBasketProduct $product) {
            return $product->quantity;
        });
    }

    public function add(int $productId): void
    {
        if ($this->userBasket === null) {
            $this->userBasket = SessionBasket::create(
                [
                    'session_uid' => $this->createSessionUidCart()
                ]
            );
        }

        $cartItem = $this->userBasket->products->firstWhere('product_id', $productId);

        $product = Product::find($productId);

        $quantityProduct = $this->productService->getQuantity($product);

        if (!$cartItem && $quantityProduct > 0) {
            $this->userBasket->products()->create([
                'product_id' => $productId,
                'quantity' => 1
            ]);
        }
    }



    public function increment(int $productId): void
    {
        $cartItem = $this->userBasket->products()->firstWhere('product_id', $productId);

        $product = Product::find($productId);

        $quantityProduct = $this->productService->getQuantity($product);

        if (!$cartItem && $quantityProduct > 0) {
            $this->add($productId);
        }
        if ($quantityProduct > $cartItem->quantity) {
            $cartItem->update(['quantity' => $cartItem->quantity + 1]);
        }
    }

    public function decrement(int $productId): void
    {
        $cartItem = $this->userBasket->products->firstWhere('product_id', $productId);

        if (!$cartItem) $this->add($productId);

        if ($cartItem->quantity - 1 > 0) $cartItem->update(['quantity' => $cartItem->quantity - 1]);

    }

    public function remove(int $productId): void
    {
        $this->userBasket->touch();
        $this->userBasket->touchOwners();

        $this->userBasket->products()->where('product_id', $productId)->delete();
    }

    public function update(Product $product, int $quantity): void
    {
        $cartItem = $this->userBasket->products()->firstWhere('product_id', $product->id);

        $quantityProduct = $this->productService->getQuantity($product);
        if (!$cartItem && $quantityProduct > 0) {
            $this->add($product->id);
        }

        if ($quantityProduct > $quantity) {
            $this->userBasket->products()->where('product_id', $product->id)->update([
                'quantity' => $quantity
            ]);
        } elseif ($quantityProduct > 0) {
            $this->userBasket->products()->where('product_id', $product->id)->update([
                'quantity' => $quantityProduct
            ]);
        }
    }


    public function destroy(): void
    {
        $this->userBasket->products()->delete();
        $this->userBasket->touch();
    }


    public function addAd(int $adId): void
    {
        //пока не надо
    }

    public function isOnlyAd(): bool
    {
        //
        return false;
    }

    public function canApplyPromocode(Promocode $promocode): bool
    {

        return $this->promocodeService->canApplyPromocode(null, $promocode);

    }

    public function addPromocode(Promocode $promocode): bool
    {
        if ($this->canApplyPromocode($promocode) === true) {

            $promocode->increment('used');

            $this->userBasket->update([
                'promocode_id' => $promocode->id
            ]);

            return true;
        }
        return false;
    }

    public function deletePromocode(): void
    {
        $this->userBasket->update([
            'promocode_id' => null
        ]);
    }

    public function getPromocode(): ?Promocode
    {
        if ($this->userBasket) {
            return $this->userBasket->promocode;
        }

        return null;
    }

    public function canApplyPaymentCertificate(PaymentCertificate $paymentCertificate): bool
    {
        return false;
    }

    public function addPaymentCertificate(PaymentCertificate $paymentCertificate): bool
    {
        return false;
    }

    public function deletePaymentCertificate(): void
    {

    }

    public function getPaymentCertificate(): ?PaymentCertificate
    {
        return null;
    }

    public function replayOrder(Order $order): void
    {

    }

    public function checkProduct(int $productId): void
    {

        $basketProduct = $this->userBasket->products()->firstWhere('product_id', $productId);

        if ($basketProduct->is_check === 1) {
            $this->userBasket->products()->where('product_id', $productId)->update(['is_check' => false]);
        } else {
            $this->userBasket->products()->where('product_id', $productId)->update(['is_check' => true]);
        }

    }

    public function checkAll(): void
    {
        $isCheck = $this->userBasket->products()->firstWhere('is_check', false);
        if ($isCheck) {
            $this->userBasket->products()->where('is_check', false)->update(['is_check' => true]);
        } else {
            $this->userBasket->products()->where('is_check', true)->update(['is_check' => false]);
        }

    }

    public function deleteSelect(): void
    {
        $this->userBasket->products()->where('is_check', true)->delete();
    }

    public function addDiscount(Discount $discount): void
    {
        $this->userBasket->update([
            'discount_id' => $discount->id
        ]);
    }

    public function deleteDiscount(): void
    {
        $this->userBasket->update([
            'discount_id' => null
        ]);
    }

    public function getDiscount(): ?Discount
    {
        if ($this->userBasket && $this->userBasket->discount_id) {
            return $this->userBasket->discount;
        }

        return null;
    }

}
