define([
    'jquery',
    'ko',
    'underscore',
    'text!Magenest_GoogleShopping/googleshopping/product/attributes.json',
    'text!Magenest_GoogleShopping/googleshopping/product/custom_attribute.json',
    'text!Magenest_GoogleShopping/googleshopping/product/installment.json',
    'text!Magenest_GoogleShopping/googleshopping/product/loyalty_points.json',
    'text!Magenest_GoogleShopping/googleshopping/product/price.json',
    'text!Magenest_GoogleShopping/googleshopping/product/product_product_detail.json',
    'text!Magenest_GoogleShopping/googleshopping/product/product_shipping.json',
    'text!Magenest_GoogleShopping/googleshopping/product/product_shipping_weight.json',
    'text!Magenest_GoogleShopping/googleshopping/product/product_subscription_cost.json',
    'text!Magenest_GoogleShopping/googleshopping/product/product_tax.json',
    'text!Magenest_GoogleShopping/googleshopping/product/product_unit_pricing_base_measure.json',
    'text!Magenest_GoogleShopping/googleshopping/product/product_unit_pricing_measure.json',
    'jquery/jquery.cookie'
], function ($,
             ko,
             _,
             productAttributes,
             CustomAttribute,
             Installment,
             LoyaltyPoints,
             Price,
             ProductProductDetail,
             ProductShipping,
             ProductShippingDimension,
             ProductShippingWeight,
             ProductSubscriptionCost,
             ProductTax,
             ProductUnitPricingBaseMeasure,
             ProductUnitPricingMeasure
) {
    'use strict';
    var dataRaw = [
        productAttributesData => JSON.parse(productAttributes),
        CustomAttributeData => JSON.parse(CustomAttribute),
        InstallmentData => JSON.parse(Installment),
        LoyaltyPointsData => JSON.parse(LoyaltyPoints),
        PriceData => JSON.parse(Price),
        ProductProductDetailData => JSON.parse(ProductProductDetail),
        ProductShippingData => JSON.parse(ProductShipping),
        ProductShippingDimensionData => JSON.parse(ProductShippingDimension),
        ProductShippingWeightData => JSON.parse(ProductShippingWeight),
        ProductSubscriptionCostData => JSON.parse(ProductSubscriptionCost),
        ProductTaxData => JSON.parse(ProductTax),
        ProductUnitPricingBaseMeasureData => JSON.parse(ProductUnitPricingBaseMeasure),
        ProductUnitPricingMeasureData => JSON.parse(ProductUnitPricingMeasure)
    ];

    return {
        getData: function (type){
            if(dataRaw.includes(type)){
                return dataRaw[type];
            }
        }
    }
});
