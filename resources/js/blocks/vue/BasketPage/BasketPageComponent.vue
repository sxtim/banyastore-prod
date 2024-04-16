<template>
    <section class="cart section">
        <div class="container">
            <h1 class="cart__title title-s">Корзина</h1>
            <div class="cart__container">
                <div class="cart__wrapper-main">
                    <basket-product-component></basket-product-component>

                    <div class="cart__main-block-wrapper">
                        <h3 class="cart__block-title">ВЫБЕРИТЕ СПОСОБ ДОСТАВКИ</h3>
                        <div class="cart__delivery-list delivery-list">
                            <div class="cart__delivery-item ">
                                <input id="radio-1" type="radio" name="radio" value="1" checked>
                                <label class="delivery-list__item" for="radio-1">
                                    <img src="/images/product-detail/delivery-ic-01.svg" alt="ic">
                                    <p>Самовывоз из пункта выдачи:<br> МО, г. Домодедово, мкр. Белые столбы, ул. Авенариуса С 6</p>
                                </label>
                            </div>
                            <div class="cart__delivery-item">
                                <input id="radio-2" type="radio" name="radio" value="2">
                                <label class="delivery-list__item" for="radio-2"><img src="/images/product-detail/delivery-ic-02.svg"
                                                                                      alt="ic">
                                    <p>Доставка ТК до терминала:
                                        <br> СДЭК, ПЭК, Байкал - сервис или Деловые линии.</p></label>
                            </div>
                            <div class="cart__delivery-item">
                                <input id="radio-3" type="radio" name="radio" value="3">
                                <label class="delivery-list__item" for="radio-3"><img src="/images/product-detail/delivery-ic-03.svg"
                                                                                      alt="ic">
                                    <p>Адресная доставка
                                        <br> транспортной компанией или курьером</p></label>
                            </div>
                        </div>
                    </div>
                    <div class="cart__main-block-wrapper">
                        <h3 class="cart__block-title">ВЫБЕРИТЕ СПОСОБ ОПЛАТЫ</h3>
                        <div class="cart__payment-method-list delivery-list">
                            <div class="cart__payment-method-item">
                                <input id="radio-4" type="radio" name="radio1" value="4" checked>
                                <label for="radio-4">
                                    <p>Картой на сайте</p> <span>Оплатить сейчас</span>
                                    <div class="payment-method-item__img-container">
                                        <img class="payment-method-item__img" src="/images/cart/mir.svg" alt="ic">
                                        <img class="payment-method-item__img" src="/images/cart/visa.svg" alt="ic">
                                    </div>
                                </label>
                            </div>
                            <div class="cart__payment-method-item">
                                <input id="radio-5" type="radio" name="radio1" value="5">
                                <label for="radio-5">
                                    <p>Через СБП</p> <span>Оплатить сейчас</span>
                                    <img class="payment-method-item__img-container" src="/images/cart/sbp.svg"
                                         alt="ic">
                                </label>
                            </div>
                            <div class="cart__payment-method-item">
                                <input id="radio-6" type="radio" name="radio1" value="6">
                                <label for="radio-6">
                                    <p>При получении</p>
                                    <span> Оплата картой, наличными или СБП в пункте выдачи</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="cart__main-block-wrapper">
                        <div class="cart__user-data">
                            <h3 class="cart__block-title">Проверьте контактные данные</h3>
                            <div class="user-data">

                                <div class="user-data__form-container">
                                    <form class="user-data__form user-form">
                                        <label class="user-form__name">Имя</label>
                                        <input type="text" name="name" placeholder="" minlength="3" axlength="20" required/>
                                        <label class="user-form__phone">Телефон</label>
                                        <input type="tel" name="reference" id="user-form-tel" placeholder="" axlength="12" required/>
                                        <label class="user-form__email">Почта</label>
                                        <input type="email" name="email" placeholder="" required/>
                                        <!--            <label class="i'm not robot">I'm not Robot</label>-->
                                        <!--            <input type="checkbox" name="I'm not Robot" id="I'm not Robot" required/>-->

                                    </form>
                                    <form class="user-data__form user-form">
                                        <label class="user-form__city">Город</label>
                                        <input type="text" name="name" placeholder="" minlength="3" axlength="20" required/>
                                        <label class="user-form__street">Улица</label>
                                        <input type="text" name="reference" placeholder="" axlength="12" required/>
                                        <label class="user-form__build">Строение/Дом</label>
                                        <input type="email" name="email" placeholder="" required/>
                                        <!--            <label class="i'm not robot">I'm not Robot</label>-->
                                        <!--            <input type="checkbox" name="I'm not Robot" id="I'm not Robot" required/>-->
                                    </form>

                                </div>
                                <!--              <button class="user-form-btn btn btn-medium" type="submit">Сохранить</button>-->
                            </div>
                        </div>
                    </div>

                </div>
                <div class="cart__sidebar-sticky">
                    <div class="cart__total-container">
                        <div class="cart__total-row">
                            <span class="cart__total-cell-title cart__total-goods">Товары:</span>
                            <span class="cart__total-cell-value cart__total-goods-sum">35 000 ₽ </span>
                        </div>
                        <div class="cart__total-row">
                            <span class="cart__total-cell-title cart__total-discount">Скидка:</span>
                            <span class="cart__total-cell-value cart__total-discount-sum">10 000 ₽</span>
                        </div>
                        <div class="cart__total-row">
                            <span class="cart__total-cell-title cart__total">Итого:</span>
                            <span class="cart__total-cell-value cart__total-sum">25 000 ₽</span>
                        </div>
                    </div>
                    <!-- acc single -->
                    <div class="cart-discount__acc js-acc-single">
                        <div class="cart-discount__acc-item js-acc-item">
                            <div class="cart-discount__acc-title js-acc-single-trigger">Промокод</div>
                            <div class="cart-discount__acc-content">
                                <form action="" class="cart-discount__form">
                                    <input type="text" class="cart-discount__input">
                                    <button class="cart__discount-btn btn btn-medium" type="submit">Применить код</button>
                                </form>
                            </div>
                        </div>
                    </div><!-- acc-single -->
                    <a href="#!" class="cart__order-btn btn btn-medium" type="button">Оформить заказ</a>
                </div>
            </div>

        </div>
    </section>

</template>

<script>

import {mapGetters} from "vuex";
import BasketProductComponent from "./BasketProductComponent";

export default {
    name: "BasketPageComponent",
    components: {BasketProductComponent},
    data() {
        return {

        }
    },
    mounted() {
        this.$store.dispatch('basket/initBasket');
    },
    computed: {
        ...mapGetters({

        })
    },

    methods: {

    },
}
</script>

<style scoped lang="scss">
.carts-product__button_disabled {
    pointer-events: none;
}

</style>
