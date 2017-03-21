/**!
 * jquery basket module for the shop module
 */

$(function(){

    // Class with common utilities
    var category = {
        /**
         * Updates global per page count for all categories
         * 
         * @param integer count New per page count
         * @param function done Callback function to be invoked when it's done
         * @return void
         */
        updatePerPageCount : function(count, done){
            $.ajax({
                type : "POST",
                url : "/module/shop/category/do/change-per-page-count",
                data : {
                    count : count
                },
                beforeSend : function(){
                    // Override with empty function
                },
                complete : function(){
                    // Override with empty function
                },
                success : function(response){
                    if (response == "1"){
                        done();
                    } else {
                        // Considered as error if response isn't 1
                        console.log(response);
                    }
                }
            });
        },
        
        /**
         * @param string sort Sort constant (represented via numeric value)
         * @param function done Callback on success to be invoked
         * @return void
         */
        updateSort : function(sort, done){
            $.ajax({
                type : "POST",
                url : "/module/shop/category/do/change-sort-action",
                data : {
                    sort : sort
                },
                beforeSend : function(){
                    // Override with empty function
                },
                complete : function(){
                    // Override with empty function
                },
                success : function(response){
                    if (response == "1"){
                        done();
                    } else {
                        // Considered as error if response isn't 1
                        console.log(response);
                    }
                }
            });
        }
    };
    
    // One static class, that holds common basket-related functions (model related logic)
    $.basket = {
        /**
         * Typical callback handler for successful requests
         */
        handleSuccess : function(response, callback){
            try {
                var data = $.parseJSON(response);
                callback(data);
                
            } catch(e){
                console.log(response);
                callback(false);
            }
        },

        /**
         * Recounts the price by associated id
         * 
         * @param string id Product id
         * @param integer qty New quantity
         * @param function Callback function to be invoked when it's done
         * @return void
         */
        recount : function(id, qty, callback){
            var self = this;
            $.ajax({
                type : "POST",
                url : "/module/shop/basket/re-count",
                data : {
                    id : id,
                    qty : qty
                },
                beforeSend : function(){
                    // This should not invoke global beforeSend() handler, so we'd override it with empty function
                },
                complete : function(){
                    // This should not invoke global complete() handler, so we'd override it with empty function too
                },
                success : function(response){
                    self.handleSuccess(response, callback);
                }
            });
        },

        /**
         * Gets basic statistic about total products count and its total price
         * 
         * @param function callback Is invoked when request is done
         * @return void
         */
        getStat : function(callback){
            var self = this;
            $.ajax({
                type : "GET",
                url : "/module/shop/basket/get-stat",
                beforeSend : function(){
                    // This should not invoke global beforeSend() handler, so we'd override it with empty function
                },
                complete : function(){
                    // This should not invoke global complete() handler, so we'd override it with empty function too
                },
                success : function(response){
                    self.handleSuccess(response, callback);
                }
            });
        },

        /**
         * Adds a product id into a basket
         * 
         * @param string id Target product id
         * @param function callback A function to be invoked on success
         * @param integer qty Quantity of ids to be added
         * @return void
         */
        add : function(id, qty, callback){
            var self = this;

            $.ajax({
                type : "POST",
                url : "/module/shop/basket/add",
                data : {
                    id : id,
                    qty : qty
                },
                beforeSend : function(){
                    // This should not invoke global beforeSend() handler, so we'd override it with empty function
                },
                complete : function(){
                    // This should not invoke global complete() handler, so we'd override it with empty function too
                },
                success : function(response){
                    // If we've got an error code
                    if ($.isNumeric(response)){
                        var log = null;

                        switch (parseInt(response)) {
                            case -1:
                                log = 'The quantity value is greater than stocking one';
                            break;

                            case 0:
                                log = 'Not enough request parameters';
                            break;
                        }

                        // Log the error code
                        console.log(log);
                    } else {
                        self.handleSuccess(response, function(data){
                            view.updateStat(data.basket);
                            view.updateAddedQv(data.product);
                        });
                    }
                }
            });
        },

        /**
         * Deletes a product by its associated id from the basket
         * 
         * @param string id Product id to be removed from a basket
         * @param callable handler Callback function invoked when it's done
         * @return void
         */
        delete : function(id, callback){
            var self = this;
            $.ajax({
                type : "POST",
                url : "/module/shop/basket/delete",
                data : {
                    id : id,
                },
                beforeSend : function(){
                    // This should not invoke global beforeSend() handler, so we'd override it with empty function
                },
                complete : function(){
                    // This should not invoke global complete() handler, so we'd override it with empty function too
                },
                success : function(response) {
                    self.handleSuccess(response, callback);
                }
            });
        },

        /**
         * Cleans the basket
         * 
         * @param function callback function to be invoked when it's done
         * @return void
         */
        clear : function(callback){
            var self = this;
            $.ajax({
                beforeSend : function(){
                    // This should not invoke global beforeSend() handler, so we'd override it with empty function
                },
                complete : function(){
                    // This should not invoke global complete() handler, so we'd override it with empty function too
                },
                type : "POST",
                url : "/module/shop/basket/clear",
                success : function(response) {
                    self.handleSuccess(response, callback);
                }
            });
        },

        /**
         * Makes an order request
         * 
         * @return void
         */
        order : function(success){
            $.ajax({
                type : "POST",
                url : "/module/shop/basket/order",
                data : $("[data-basket-form='order']").serialize(),
                success : function(response) {
                    // TODO
                    success(response);
                }
            });
        }
    };
    
    var currency = {
        /**
         * Returns all available currencies
         * 
         * @return object
         */
        getAll: function(){
            var $source = $("input[data-currency='source']");

            // Do processing in case source is found
            if ($source.length) {
                var jsonString = $source.val();

                try {
                    return $.parseJSON(jsonString);
                } catch(e) {
                    console.log('Error while paring source string');
                    return false;
                }

            } else {
                // No currency input
                return false;
            }
        },

        /**
         * Converts current value to currency by code
         * 
         * @param string code Currency code
         * @param string value
         * @param boolean format Whether to format output
         * @return float
         */
        convert: function(value, code, format){
            // Get all currencies
            var currencies = this.getAll();

            // Get currency value by its provided code
            var currency = typeof currencies[code] != undefined ? currencies[code] : null;

            // Make sure it's not null. If it is, then invalid code provided
            if (currency === null) {
                console.log('Invalid currency code provided. Halted processing');
                return false;
            }

            // Count result
            result = parseFloat(value) * currency;

            // If not provided, then assume true by default
            if (format == undefined) {
                format = true;
            }

            // Format numbers by default
            if (format) {
                result = parseFloat(result).toLocaleString();
            }

            return result;
        }
    };

    // Summary class
    var summary = {
        /**
         * Returns summary element
         * 
         * @return object
         */
        getElement: function(){
            return $("[data-basket-label='summary-price']");
        },

        /**
         * Returns current value
         * 
         * @return string
         */
        getCurrentValue: function(){
            return this.getElement().data('currency-input-value');
        },

        /**
         * Update summary value
         * 
         * @param mixed newPrice
         * @return void
         */
        updateValue: function(newPrice){
            var $summary = this.getElement();

            $summary.attr('data-currency-input-value', newPrice);
            $summary.data('currency-input-value', newPrice);
            $summary.text(newPrice.toLocaleString());
        },

        /**
         * Updates summary element
         * 
         * @return void
         */
        increment: function(value){
            var $summary = this.getElement();

            if ($summary.data('currency-input-value')) {
                // Grab initial price and add delivery price
                var initialPrice = parseFloat($summary.data('summary-initial-price'));
                var newPrice = initialPrice + parseFloat(value);

                // Update currency value
                this.updateValue(newPrice);
            }
        }
    };
    
    // View-related logic
    var view = {
        /**
         * Update values in "Successfully added" modal dialog
         * 
         * @param object product JSON object received from back-end
         * @return void
         */
        updateAddedQv: function(product){
            // Create price
            if (product.stokePrice != 0){
                var price = product.stokePrice;
            } else {
                var price = product.regularPrice;
            }

            // For QV
            $("[data-qv-product='name']").text(product.name);
            $("[data-qv-product='qty']").text(product.qty);
            $("[data-qv-product='price']").text(price);
            $("[data-qv-product='cover']").attr('src', product.cover);
        },

        /**
         * Update statistic
         * 
         * @param object data
         * @return void
         */
        updateStat : function(data){
            var $qty = $("[data-basket-label='total-products-qty']");
            var $price = $("[data-basket-label='total-products-price']");

            if ($qty.data('inflect-count')) {
                $qty.data('inflect-count', data.totalQuantity);
            } else {
                // Do not update the inner text if inflector is initialized
                $qty.text(data.totalQuantity);
            }

            if ($price.data('currency-input-value')) {
                $price.data('currency-input-value', parseFloat(data.totalPrice));
            }

            // If we have special labels in a mark-up, then we need to update them accordingly
            $price.text(parseFloat(data.totalPrice).toLocaleString());
        },

        /**
         * Grabs all nodes associated with provided product id
         * 
         * @param string id Product id
         */
        getNodesByProductId : function(id){
            return $("[data-basket-product-id=" + "'" + id + "'" + "]");
        },

        /**
         * Grabs all nodes associated with provided product id and filters result by provided selector
         * 
         * @param string id Product id
         * @param string selector
         */
        getNodesByProductIdWithFilter : function(id, selector){
            return this.getNodesByProductId(id).filter(selector);
        },

        /**
         * Grabs product id from an element
         * 
         * @param NodeElement element
         * @return string
         */
        grabProductId : function(element){
            return $(element).data("basket-product-id");
        },

        /**
         * Handler called when invoking products removal
         * 
         * @param string id Product id
         * @param string data Server's response
         * @return void
         */
        onRemoval : function(id, data){
            // Ensure we've got what we expected first
            if (data !== false){
                // If a user removed all product from the basket, the we need to refresh a page
                if (data.totalQuantity == 0){
                    window.location.reload();
                } else {
                    // Otherwise just update a table
                    this.updateStat(data);
                    this.updateCurrency();

                    $row = this.getNodesByProductIdWithFilter(id, "[data-basket-type='container']");
                    $row.hide(500, function(){
                        // Remove a row
                        $(this).empty();
                    });
                }
            } else {
                // We got something we didn't expect, so just log it for now
                console.log(data);
            }
        },

        /**
         * Grabs product's quantity
         * 
         * @param string id Product id
         * @return integer Actual quantity if found, otherwise 1 as a default value
         */
        grabQtyByProductId : function(id){
            // By default, we always have one quantity of a product we're going to add
            var qty = 1;

            // Try finding an element which represents a new quantity
            var $qty = this.getNodesByProductIdWithFilter(id, "[data-basket-input='qty']");

            // If found, then simply alter default value
            if ($qty.length > 0){
                qty = $qty.val();
            }

            return qty;
        },

        /**
         * Update currency in real-time
         * 
         * @return void
         */
        updateCurrency: function(){
            // Local configuration
            var config = {
                inputSelector: "[data-currency-input-id]",
                outputSelector: "[data-currency-output-for]",
                codeSelector: "[data-currency-code]"
            };

            $(config.inputSelector).each(function(){
                // Grab required parameters
                var id = $(this).data('currency-input-id');
                var value = $(this).data('currency-input-value');
                var format = $(this).data('currency-format-value');
                var code = $(this).data('currency-code');

                // If its undefined, fall back to text
                if (!value) {
                    value = $(this).text();
                }

                // Get currency value by its provided code
                var result = currency.convert(value, code);

                // Find associated outputting element
                $("[data-currency-output-for='" + id + "']").text(result);
            });
        }
    };

    // Update currency as well
    view.updateCurrency();

    $("a[data-product-large-image]").click(function(event){
        event.preventDefault();

        // Get link to that larger image
        var src = $(this).data('product-large-image');
        var $cover = $("[data-product-image='cover']");

        // Ensure cover element exists
        if ($cover.length == 0) {
            console.log('You need to provide data attribute to the cover image');
        } else {
            // Now simply change src attribute value in cover image
            $cover.attr('src', src);
        }
    });
    
    
    $("[data-category-option='per-page-count']").change(function(event){
        // New count
        var count = $(this).val();

        category.updatePerPageCount(count, function(){
            window.location.reload();
        });
    });
    
    
    $("[data-category-option='sort']").change(function(event){
        var sort = $(this).val();
        category.updateSort(sort, function(){
            window.location.reload();
        });
    });
    
    $("[data-basket-button='order']").click(function(event){
        event.preventDefault();
        // Grab parent form
        var $form = $(this).closest('form');

        $.basket.order(function(response){
            // 1 means success
            if (response == "1"){
                window.location.reload();
            } else {
                $.getValidator($form).handleAll(response);
            }
        });
    });
    
    
    $("[data-basket-button='clear-without-confirm']").click(function(event){
        event.preventDefault();

        $.basket.clear(function(data){
            window.location.reload();
        });
    });
    
    
    $("[data-basket-button='product-delete-with-confirm']").click(function(event){
        event.preventDefault();
        var id = view.grabProductId(this);
        
        // Ensure the previous listener is removed, and attach a new one
        $("[data-basket-button='product-delete-confirm-yes']").off('click').click(function(event){
            $.basket.delete(id, function(data){
                view.onRemoval(id, data);
            });
        });
    });
    
    $("[data-basket-button='product-delete-without-confirm']").click(function(event){
        event.preventDefault();
        var id = view.grabProductId(this);
        
        $.basket.delete(id, function(data){
            view.onRemoval(id, data);
        });
    });
    
    // Product recount button
    $(document).on('click', "[data-basket-button='product-recount']", function(){
        event.preventDefault();
        // Current product's id
        var id = view.grabProductId(this);

        // Find all nodes corresponding to current product id
        var $productNodes = view.getNodesByProductId(id);

        // New quantity
        var qty = $productNodes.filter("[data-basket-input='recount']").val();

        $.basket.recount(id, qty, function(data){
            if (data !== false) {
                view.updateStat(data.all);
                // Now change subTotalCount label
                $productNodes.filter("[data-basket-label='sub-total-price']").text(data.product.totalPrice);
            }
        });
    });

    // Add to basket button
    $(document).on('click', "[data-basket-button='add']", function(){
        // We might be dealing with <a> tag, so it's better to ensure that default event is prevented
        event.preventDefault();

        // Grab product's id we're adding to basket
        var id = view.grabProductId(this);
        var qty = view.grabQtyByProductId(id);
        var $self = $(this);

        // If provided
        var disabledClass = $self.data('basket-button-disabled-class');

        // If has disabled class, then cancel all associated click events, and disable the button itself
        if (disabledClass){
            $self.addClass(disabledClass).click(function(event){
                event.preventDefault();
                event.stopPropagation();

                return false;
            });
        }

        // Now just add it, and when its added, react to it using callback function (which holds data JSON object, or holds false)
        $.basket.add(id, qty, function(data){
            if (data !== false) {
                view.updateStat(data);
            } else {
                console.log('Failure when retrieving data from a basket: ' + data);
            }
        });
    });
    
    // Quick view button
    $(document).on('click', '[data-button="quick-view"]', function(event){
        event.preventDefault();

        $.ajax({
            cache: true,
            url: '/module/shop/product/quick-view/',
            data: {
                id: $(this).attr('data-product-id'),
                size: $(this).attr('data-cover-size')
            },
            success: function(response){
                $("#quickViewModal .modal-body").html(response);
            }
        });
    });

    // Discount coupon handler
    (function(){

        // Local configuration
        var config = {
            interval: 2000,
            couponInputSelector: "[data-shop-input='coupon']",
            discountLabelSelector: "[data-shop-label='coupon-discount']",
            successInputClass: "checkout-promo-success"
        };

        var $coupon = $(config.couponInputSelector);
        var timer = null;

        // If the input found, handle it
        if ($coupon.length) {
            $coupon.blur(function(){
                clearInterval(timer);
                timer = null;
            });

            $coupon.keyup(function(){
                var value = $(this).val();

                // Lazy timer initialization
                if (timer === null) {
                    timer = setInterval(function(){
                        $.ajax({
                            cache: true,
                            url: "/module/shop/coupon/check/",
                            data: {
                                code: $(config.couponInputSelector).val()
                            },
                            success: function(response){
                                // 0 means failure
                                if (response == 0) {
                                    $coupon.removeClass(config.successInputClass);
                                } else {
                                    clearInterval(timer);
                                    $coupon.addClass(config.successInputClass);

                                    // Update the label
                                    $(config.discountLabelSelector).text(response);

                                    // Update currency as well
                                    view.updateCurrency();
                                }
                            }
                        });

                    }, config.interval);
                }
            });
        }

    })($, view);

    // Delivery payment changer
    (function(){
        // Local configuration
        var config = {
            sourceSelector: "select[data-delivery-type='input']",
            deliveryPriceLabelSelector: "[data-shop-label='delivery-price']"
        };

        $(config.sourceSelector).change(function(){
            // Find current active option
            var $option = $(this).find(':selected');
            var value = $option.data('delivery-price');

            $(config.deliveryPriceLabelSelector).text(value);

            summary.increment(value);

            // Update currency as well
            view.updateCurrency();

        }).trigger('change');

    })($, view, summary);
});
