<?php
namespace Magenest\GoogleShopping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class GoogleShoppingAttribute extends AbstractHelper
{
    public function getGoogleShoppingAttribute()
    {
        return [
            [
                'label' => 'Basic product data',
                'attributes' => [
                    [
                        'label'       => 'Offer ID (g:id)',
                        "name"        => "offerId",
                        "feed_name"   => "",
                        "format"      => "required"
                    ],
                    [
                        'label'       => 'Product title (g:title)',
                        "name"        => "title",
                        "feed_name"   => "g:title",
                        "format"      => "required"
                    ],
                    [
                        'label'       => 'Product description (g:description)',
                        "name"        => "description",
                        "feed_name"   => "g:description",
                        "format"      => "required",
                    ],
                    [
                        'label'       => 'Product URL (g:link)',
                        "name"        => "link",
                        "feed_name"   => "g:link",
                        "format"      => "required",
                    ],
                    [
                        'label'       => 'Main image UR (g:image_link)',
                        "name"        => "image_link",
                        "feed_name"   => "g:image_link",
                        "format"      => "required",
                    ],
                    [
                        'label'     => 'Additional image URL (g:additional_image_link)',
                        "name"      => "additionalImageLinks",
                        "feed_name" => "g:additional_image_link",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Product URL mobile (g:mobile_link)',
                        "name"      => "mobileLink",
                        "feed_name" => "g:mobile_link",
                        "format"    => "optional",
                    ],
                ],
            ],
            [
                'label'      => 'Price & availability',
                'attributes' => [
                    [
                        'label'       => 'Stock status (g:availability)',
                        "name"        => "availability",
                        "feed_name"   => "g:availability",
                        "format"      => "required",
                    ],
                    [
                        'label'     => 'Availability date (g:availability_date)',
                        "name"      => "availabilityDate",
                        "feed_name" => "g:availability_date",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Expiration date (g:expiration_date)',
                        "name"      => "expirationDate",
                        "feed_name" => "g:expiration_date",
                        "format"    => "optional",
                    ],
                    [
                        'label'       => 'Price (g:price)',
                        "name"        => "price",
                        "feed_name"   => "g:price",
                        "format"      => "required",
                    ],
                    [
                        'label'       => 'Sale price (g:sale_price)',
                        "name"        => "salePrice",
                        "feed_name"   => "g:sale_price",
                        "format"      => "optional",
                    ],
                    [
                        'label'     => 'Loyalty points (g:loyalty_points)',
                        "name"      => "loyalty_points",
                        "feed_name" => "g:loyalty_points",
                        "format"    => "optional",
                    ],
                ]
            ],
            [
                'label'        => 'Product category',
                'attributes'   => [
                    [
                        'label'       => 'Google product category (g:google_product_category)',
                        "name"        => "googleProductCategory",
                        "feed_name"   => "g:google_product_category",
                        "format"      => "required",
                    ],
                    [
                        'label'       => 'Product type (g:product_type)',
                        "name"        => "productTypes",
                        "feed_name"   => "g:product_type",
                        "format"      => "optional",
                    ],
                ],
            ],
            [
                'label'      => 'Price & availability',
                'attributes' => [
                    [
                        'label'     => 'Brand (g:brand)',
                        "name"      => "brand",
                        "feed_name" => "g:brand",
                        "format"    => "required",
                    ],
                    [
                        'label'     => 'Gtin (g:gtin)',
                        "name"      => "gtin",
                        "feed_name" => "g:gtin",
                        "format"    => "required",
                    ],
                    [
                        'label'     => 'MPN (g:mpn)',
                        "name"      => "mpn",
                        "feed_name" => "g:mpn",
                        "format"    => "required",
                    ],
                    [
                        'label'       => 'Identifier exists (g:identifier_exists)',
                        "name"        => "identifierExists",
                        "feed_name"   => "g:identifier_exists",
                        "format"      => "optional",
                    ],
                ]
            ],
            [
                'label'      => 'Detailed product description',
                'attributes' => [
                    [
                        'label'       => 'Condition (g:condition)',
                        "name"        => "condition",
                        "feed_name"   => "g:condition",
                        "format"      => "required",
                    ],
                    [
                        'label'     => 'Adult (g:adult)',
                        "name"      => "adult",
                        "feed_name" => "g:adult",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Multipack (g:multipack)',
                        "name"      => "multipack",
                        "feed_name" => "g:multipack",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Is bundle (g:is_bundle)',
                        "name"      => "isBundle",
                        "feed_name" => "g:is_bundle",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Energy efficiency class (g:energy_efficiency_class)',
                        "name"      => "energyEfficiencyClass",
                        "feed_name" => "g:energy_efficiency_class",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Minimum energy efficiency class (g:min_energy_efficiency_class)',
                        "name"      => "minEnergyEfficiencyClass",
                        "feed_name" => "g:min_energy_efficiency_class",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Maximum energy efficiency class (g:max_energy_efficiency_class)',
                        "name"      => "maxEnergyEfficiencyClass",
                        "feed_name" => "g:max_energy_efficiency_class",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Age group (g:age_group)',
                        "name"      => "ageGroup",
                        "feed_name" => "g:age_group",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Color (g:color)',
                        "name"      => "color",
                        "feed_name" => "g:color",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Gender (g:gender)',
                        "name"      => "gender",
                        "feed_name" => "g:gender",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Material (g:material)',
                        "name"      => "material",
                        "feed_name" => "g:material",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Pattern (g:pattern)',
                        "name"      => "pattern",
                        "feed_name" => "g:pattern",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Size (g:size)',
                        "name"      => "sizes",
                        "feed_name" => "g:size",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Size type (g:size_type)',
                        "name"      => "sizeType",
                        "feed_name" => "g:size_type",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Size system (g:size_system)',
                        "name"      => "sizeSystem",
                        "feed_name" => "g:size_system",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Item group ID (g:item_group_id)',
                        "name"      => "itemGroupId",
                        "feed_name" => "g:item_group_id",
                        "format"    => "optional",
                    ],
                ]
            ],
            [
                'label'      => 'Shopping campaigns and other configurations',
                'attributes' => [
                    [
                        'label'     => 'Ads redirect (g:ads_redirect)',
                        "name"      => "adsRedirect",
                        "feed_name" => "g:ads_redirect",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Custom label 0 (g:custom_label_0)',
                        "name"      => "customLabel0",
                        "feed_name" => "g:custom_label_0",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Custom label 1 (g:custom_label_1)',
                        "name"      => "customLabel1",
                        "feed_name" => "g:custom_label_1",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Custom label 2 (g:custom_label_2)',
                        "name"      => "customLabel2",
                        "feed_name" => "g:custom_label_2",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Custom label 3 (g:custom_label_3)',
                        "name"      => "customLabel3",
                        "feed_name" => "g:custom_label_3",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Custom label 4 (g:custom_label_4)',
                        "name"      => "customLabel4",
                        "feed_name" => "g:custom_label_4",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Promotion ID (g:promotion_id)',
                        "name"      => "promotionIds",
                        "feed_name" => "g:promotion_id",
                        "format"    => "optional",
                    ],
                ]
            ],
            [
                'label'      => 'Destinations',
                'attributes' => [
                    [
                        'label'     => 'Excluded destination (g:excluded_destination)',
                        "name"      => "excludedDestinations",
                        "feed_name" => "g:excluded_destination",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Included destination (g:included_destination)',
                        "name"      => "includedDestinations",
                        "feed_name" => "g:included_destination",
                        "format"    => "optional",
                    ]
                ]
            ]
        ];
    }
}
