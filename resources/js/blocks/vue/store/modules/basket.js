import axios from 'axios'

export default {
    state: () => ({
        fullList: [],
        checkIsLimit: null,
        products: [],
        discount: '',
        checkAuth: false,
        purveyors: {
            hasEcomilyProducts: false,
            hasKedrProducts: false,
            hasGlobalProducts: false,
            onlyGlobalProducts: false
        },
        accrualPrice: 0,
        deliveryPrice: 0,
        promocode: '',
        certificate: '',
        applyCertificateStatus: false,
        certificateError: '',
        certificateSuccess: '',
        promocodeMessage: '',
        applyPromocodeStatus: false,
        promoClass: '',
        promoMessageClass: '',
    }),
    mutations: {
        setProducts(state, products) {
            state.products = products;
        },
        setFullList(state, fullList) {
            state.fullList = fullList;
        },
        setCertificateError(state, value) {
            state.certificateError = value
        },
        setPromocode(state, promocode) {
            state.promocode = promocode;
        },
        setCertificate(state, certificate) {
            state.certificate = certificate;
            // if (certificate) {
            //     state.applyCertificateStatus = true;
            // }
        },
        updatePromocode(state, promocode) {
            state.promoMessageClass = '';
            state.promoClass = '';
            state.promocodeMessage = '';
            state.promocode = promocode;
        },
        setPromoClass(state, promoClass) {
            state.promoClass = promoClass
        },
        setDeliveryPrice(state, deliveryPrice) {
            state.deliveryPrice = deliveryPrice;
        },
        setPurveyorFlags(state, purveyorFlags) {
            state.purveyors.hasEcomilyProducts = purveyorFlags.hasEcomilyProducts
            state.purveyors.hasKedrProducts = purveyorFlags.hasKedrProducts
            state.purveyors.hasGlobalProducts = purveyorFlags.hasGlobalProducts
            state.purveyors.onlyGlobalProducts = purveyorFlags.onlyGlobalProducts
        },
        setAccrualPrice(state, accrualPrice) {
            state.accrualPrice = accrualPrice
        },
        setCheckAuth(state, checkAuth) {
            state.checkAuth = checkAuth
        },
        setDiscount(state, discount) {
            state.discount = discount
        },
        setCheckIsLimit(state, checkIsLimit) {
            state.checkIsLimit = checkIsLimit
        }
    },
    actions: {
        checkAll({commit, state}) {
            axios.post(window.location.origin + `/basket/check-all`).then(response => {
                commit('setProducts', response.data.items);
                commit('setFullList', response.data.fullList);
                commit('setDiscount', response.data.discount);
                commit('setCheckIsLimit', response.data.checkLimit)
            })
        },
        deleteSelect({commit, state}) {
            axios.post(window.location.origin + `/basket/delete-select`).then(response => {
                commit('setProducts', response.data.items);
                commit('setFullList', response.data.fullList);
                commit('setDiscount', response.data.discount);
                commit('setCheckIsLimit', response.data.checkLimit)

                window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                    "ecommerce": {
                        "currencyCode": "RUB",
                        "remove": {
                            "products": response.data.deletedProducts
                        }
                    }
                });


                if ((!response.data.fullList || response.data.fullList.length === 0) && window.location.href === window.location.origin + `/basket`) {
                    window.location.href = window.location.origin
                }
            })
        },
        getBasketProductList({commit, state}) {
            axios.get(window.location.origin + `/basket/get-products`).then(response => {
                commit('setProducts', response.data.items);
                commit('setFullList', response.data.fullList);
                commit('setCheckIsLimit', response.data.checkLimit);
                commit('setPromocode', response.data.promocode);
                if (response.data.promocode) {
                    state.applyPromocodeStatus = true
                    state.promoClass = 'promo-code_success'
                }
                commit('setCertificate', response.data.certificate)
                commit('setCheckAuth', response.data.auth)
                if (response.data.certificate) {
                    state.applyCertificateStatus = true
                }
                commit('setAccrualPrice', response.data.accrualPrice)
                commit('setDiscount', response.data.discount)
            })
        },
        removeProduct({dispatch, commit, state}, productId) {
            axios.post(window.location.origin + `/basket/remove-product`, {
                product_id: productId.productId
            }).then(response => {
                commit('setProducts', response.data.items)
                commit('setFullList', response.data.fullList);
                commit('setDiscount', response.data.discount);
                commit('setCheckIsLimit', response.data.checkLimit);


                window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                    "ecommerce": {
                        "currencyCode": "RUB",
                        "remove": {
                            "products": [response.data.product]
                        }
                    }
                });

                // commit('updatePaymentMethod', constants.payment_methods.electronic)

                if ((!response.data.fullList || response.data.fullList.length === 0) && window.location.href === window.location.origin + `/basket`) {
                    window.location.href = window.location.origin
                }
            });
        },
        checkProduct({dispatch, commit, state}, productId) {
            axios.post(window.location.origin + `/basket/check-product`, {
                product_id: productId.productId
            }).then(response => {
                commit('setProducts', response.data.items)
                commit('setFullList', response.data.fullList);
                commit('setDiscount', response.data.discount)
                commit('setCheckIsLimit', response.data.checkLimit)
            });
        },
        incrementProduct({dispatch, commit, state, store}, productId) {
            axios.post(window.location.origin + `/basket/increment`, {
                product_id: productId.productId
            }).then(response => {
                commit('setDiscount', response.data.discount)
                commit('setFullList', response.data.fullList);
                commit('setProducts', response.data.items);
                commit('setCheckIsLimit', response.data.checkLimit)
            });
        },
        addProduct({commit}, productId) {
            axios.post(window.location.origin + `/basket/add`, {
                product_id: productId.productId
            }).then(response => {
                commit('setProducts', response.data.items);
                commit('setFullList', response.data.fullList);
                commit('setDiscount', response.data.discount)
                commit('setCheckIsLimit', response.data.checkLimit)

                window.dataLayer = window.dataLayer || [];
                dataLayer.push({
                    "ecommerce": {
                        "currencyCode": "RUB",
                        "add": {
                            "products": [response.data.product]
                        }
                    }
                });
            });
        },
        decrementProduct({dispatch, commit, state}, productId) {
            axios.post(window.location.origin + `/basket/decrement`, {
                product_id: productId.productId
            }).then(response => {
                commit('setDiscount', response.data.discount)
                commit('setFullList', response.data.fullList);
                commit('setProducts', response.data.items);
                commit('setCheckIsLimit', response.data.checkLimit)
            });
        },
        updateProduct({dispatch, commit, state}, {productId, quantity}) {
            axios.post(window.location.origin + `/basket/update`, {
                product_id: productId,
                quantity: quantity
            }).then(response => {
                commit('setDiscount', response.data.discount)
                commit('setFullList', response.data.fullList);
                commit('setProducts', response.data.items);
                commit('setCheckIsLimit', response.data.checkLimit)
            });
        },
        applyPromocode({dispatch, state}) {
            axios.post(window.location.origin + `/basket/apply-promocode`, {
                code: state.promocode
            }).then(response => {
                if (response.data.status === true) {
                    state.applyPromocodeStatus = true
                    state.promoClass = 'promo-code_success'
                    state.promoMessageClass = 'promocode__input'
                    dispatch('getBasketProductList');
                } else {
                    state.promoClass = 'promo-code_error'
                    state.promoMessageClass = 'promocode__message-error'
                }
                state.promocodeMessage = response.data.message;
            });
        },
        applyCertificate({dispatch, state, commit}) {
            axios.post(window.location.origin + `/basket/apply-certificate`, {
                certificate: state.certificate
            }).then(response => {
                if (response.data.status) {
                    state.applyCertificateStatus = true
                    commit('setCertificateError','')
                    state.certificateSuccess = response.data.message
                    dispatch('getBasketProductList');
                } else {
                    state.applyCertificateStatus = false
                    state.certificateSuccess = ''
                    commit('setCertificateError',response.data.message)
                }
            });
        },
        deleteCertificate({dispatch, state, commit}) {
            axios.post(`/basket/delete-certificate`, {
                certificate: state.certificate
            }).then(response => {
                if (response.data.status) {
                    state.certificate = null;
                    state.applyCertificateStatus = false
                    commit('setCertificateError','')
                    state.certificateSuccess = ''
                    dispatch('getBasketProductList');
                }
            });
        },
        deletePromocode({dispatch, state}) {
            axios.post(window.location.origin + `/basket/delete-promocode`, {
                code: state.promocode
            }).then(response => {
                if (response.data.status) {
                    state.applyPromocodeStatus = false;
                    state.promocode = null;
                    state.promoMessageClass = '';
                    state.promoClass = '';
                    state.promocodeMessage = '';
                    state.promocodeMessage = response.data.message;
                    dispatch('getBasketProductList');
                }
            });
        },
        deleteOzonData({commit}) {
            commit('updateOzonData', null)
            commit('setDeliveryPrice', null)
        }
        ,
        boxberryData ({commit}) {
            commit('updateBoxberryData', null)
            commit('setDeliveryPrice', null)
        }
    },
    getters: {
        getProducts(state) {
            return state.products;
        },
        getFullList(state) {
            return state.fullList;
        },
        getCheckAll(state) {
            let check = true;
            state.fullList.forEach(product => {
                if (product.isCheck === false) {
                    check = false;
                }
            });
            return check
        },
        getTotalQuantity(state) {
            let totalQuantity = 0;
            for (let key in state.products) {
                totalQuantity += state.products[key].quantity;
            }
            return totalQuantity;
        },
        getTotalQuantityFullList(state) {
            let totalQuantity = 0;
            state.fullList.forEach(product => {
                totalQuantity += product.quantity;
            });
            return totalQuantity;
        },
        getProductsTotal(state) {
            let total = 0;
            state.products.forEach(product => {
                total += product.quantity * product.price;
            });

            return Number(total.toFixed(2))
        },
        getPromocodeMessage(state) {
            return state.promocodeMessage;
        },
        getApplyPromocodeStatus(state) {
            return state.applyPromocodeStatus;
        },
        getApplyCertificateStatus(state) {
            return state.applyCertificateStatus
        },
        getPromoClass(state) {
            return state.promoClass;
        },
        getPromoMessageClass(state) {
            return state.promoMessageClass;
        },
        getTotalDiscount(state) {
            let totalDiscount = 0;
            state.products.forEach(product => {
                totalDiscount += product.discount * product.quantity;
            });
            return totalDiscount;
        },
        getPromoPriceTotal(state) {
            let totalPromoPrice = 0;
            state.products.forEach(product => {
                totalPromoPrice += product.promoPrice * product.quantity;
            });
            return Number(totalPromoPrice.toFixed(2));
        },
        getPromocode(state) {
            return state.promocode;
        },
        getDeliveryPrice(state) {
            return state.deliveryPrice;
        },
        getWeight(state) {
            let weight = 0;

            state.products.forEach(function (product) {
                weight += Number(Number(product.dimensions.weight) * Number(product.quantity));
            })

            return Number(weight);
        },
        getDimensions(state) {
            let length = 0;
            let width = 0;
            let height = 0;

            state.products.forEach(function (product) {
                length += Number(Number(product.dimensions.length) * Number(product.quantity));
                width += Number(Number(product.dimensions.width) * Number(product.quantity));
                height += Number(Number(product.dimensions.height) * Number(product.quantity));
            })

            return {
                'length': Number(length),
                'width': Number(width),
                'height': Number(height)
            };
        },
        getDimensionsBaikal(state) {
            let maxLength = 0;
            let maxWidth = 0;
            let volume = 0;

            state.products.forEach(function (product) {

                if (Number(product.dimensions.length) > maxLength) {
                    maxLength = Number(product.dimensions.length)
                }

                if (Number(product.dimensions.width) > maxWidth) {
                    maxWidth = Number(product.dimensions.width)
                }

                volume += Number(product.dimensions.length) * Number(product.dimensions.width) * Number(product.dimensions.height)
            })


            return {
                'maxLength': maxLength,
                'maxWidth': maxWidth,
                'volume': Number(volume)
            };
        },
        getPackages(state) {
            return state.products.filter(function (product) {
                return product.purveyor.inn === constants.global_inn
            }).map(function (product) {
                let packageString = "[" + product.dimensions.weight + "," + product.dimensions.width + "," + product.dimensions.height + "," + product.dimensions.length + "],";
                let resultString = packageString.repeat(product.quantity);
                return resultString.substring(0, resultString.length - 1)
            })
        },
        sortByRootCategoryProducts(state) {
            return state.products.reduce((group, product) => {
                const {rootCategory} = product;
                group[rootCategory] = group[rootCategory] ?? [];
                group[rootCategory].push(product);
                return group;
            }, {});
        },
        sortByRootCategoryFullList(state) {
            return state.fullList.reduce((group, product) => {
                const {rootCategory} = product;
                group[rootCategory] = group[rootCategory] ?? [];
                group[rootCategory].push(product);
                return group;
            }, {});
        },
        sortByPurveyorFullList(state) {
            return state.fullList.reduce((group, product) => {
                const {purveyor} = product;
                group[purveyor.name_sait] = group[purveyor.name_sait] ?? [];
                group[purveyor.name_sait].push(product);
                return group;
            }, {});
        },
        getPurveyors(state) {
            return state.purveyors
        },
        getCertificate(state) {
            return state.certificate
        },
        getCertificateDiscount(state) {
            let total = 0;
            state.products.forEach(product => {
                total += product.quantity * product.certificateDiscountPerPiece;
            });

            return total
        },
        getPromocodeDiscount(state) {
            let total = 0;
            state.products.forEach(product => {
                total += product.quantity * product.promocodeDiscountPerPiece;
            });

            return Number(total.toFixed(2));
        },
        getAccrualPrice(state) {
            return state.accrualPrice
        },
        getCashbackTotal(state, getters, rootState, rootGetters) {
            if (rootGetters['order/getOutPutSum'] >= getters['getProductsTotal']) {
                return getters['getProductsTotal'] - getters['getTotalQuantity']
            } else {
                return rootGetters['order/getOutPutSum']
            }
        },
        getTotalSum(state, getters, rootState, rootGetters) {
            return rootGetters['order/getPayWithCashback']
                ? getters['getProductsTotal'] - getters['getCashbackTotal'] + getters['getDeliveryPrice']
                : getters['getProductsTotal'] + getters['getDeliveryPrice'];
        },
        getCheckAuth(state) {
            return state.checkAuth
        },
        getDiscount(state) {
            return state.discount
        },
        getCheckIsLimit(state) {
            return state.checkIsLimit
        }
    }
};
