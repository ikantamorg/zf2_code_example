<?php

namespace Seo;

return array(
    'controllers' => array(
        'invokables' => array(
            'Seo\Sitemap'                           => 'Seo\Controller\Sitemap',

            'Admin\Seo\Options'                     => 'Seo\Controller\Admin\Options',

            'Widget\Seo\AdminDashboardPiwik'       => 'Seo\Widget\AdminDashboardPiwik'
        ),
    ),

    'router' => array(
        'routes' => array(
            'admin' => [
                'child_routes' => [
                    'seo' => [
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/seo'
                        ),
                        'may_terminate' => true,
                        'child_routes' => [
                            'options' => [
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/options[/:action]',
                                    'defaults' => array(
                                        'controller' => 'Admin\Seo\Options',
                                        'action' => 'index',
                                    ),
                                ),
                            ],
                        ]
                    ]
                ]
            ],
            'sitemap' => [
                'type' => 'Literal',
                'options' => array(
                    'route' => '/sitemap.xml',
                    'defaults' => array(
                        'controller' => 'Seo\Sitemap',
                        'action' => 'index',
                    ),
                ),

            ]
        )
    ),

    'admin' => [
        'menu' => [
            'seo' => array(
                'order' => 3,
                'label' => 'Seo',
                'route' => 'admin/seo/options',
                'icon' => 'fa fa-book',
                'pages' => [
                    'options' => [
                        'label' => 'Options',
                        'route' => 'admin/seo/options',
                        'icon' => ''
                    ]
                ]
            ),
        ],
        'dashboard_widget' => [
            [
                'module' => 'Seo',
                'widget_name' => 'AdminDashboardPiwik'
            ]
        ]
    ],

    'seo_url' => array(
        // minimal length of given string
        'min_length' => 2,

        // max alloved length for result URL
        'max_length' => 55,

        // separator to replace spaces
        'separator' => '-',

        // default encoding for given string, need to calculate in correct way the min_length of string
        'string_encoding' => 'UTF-8',

        // array of chars to be replaces with latin chars
        'foreign_chars' => array(
            '/ä|æ|ǽ/' => 'ae',
            '/ö|œ/' => 'oe',
            '/ü/' => 'ue',
            '/Ä/' => 'Ae',
            '/Ü/' => 'Ue',
            '/Ö/' => 'Oe',
            '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|Α|Ά|Ả|Ạ|Ầ|Ẫ|Ẩ|Ậ|Ằ|Ắ|Ẵ|Ẳ|Ặ|А/' => 'A',
            '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|α|ά|ả|ạ|ầ|ấ|ẫ|ẩ|ậ|ằ|ắ|ẵ|ẳ|ặ|а/' => 'a',
            '/Б/' => 'B',
            '/б/' => 'b',
            '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
            '/ç|ć|ĉ|ċ|č/' => 'c',
            '/Д/' => 'D',
            '/д/' => 'd',
            '/Ð|Ď|Đ|Δ/' => 'Dj',
            '/ð|ď|đ|δ/' => 'dj',
            '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Ε|Έ|Ẽ|Ẻ|Ẹ|Ề|Ế|Ễ|Ể|Ệ|Е|Ё|Э/' => 'E',
            '/è|é|ê|ë|ē|ĕ|ė|ę|ě|έ|ε|ẽ|ẻ|ẹ|ề|ế|ễ|ể|ệ|е|ё|э/' => 'e',
            '/Ф/' => 'F',
            '/ф/' => 'f',
            '/Ĝ|Ğ|Ġ|Ģ|Γ|Г/' => 'G',
            '/ĝ|ğ|ġ|ģ|γ|г/' => 'g',
            '/Ĥ|Ħ/' => 'H',
            '/ĥ|ħ/' => 'h',
            '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|Η|Ή|Ί|Ι|Ϊ|Ỉ|Ị|И|Й/' => 'I',
            '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|η|ή|ί|ι|ϊ|ỉ|ị|и|й/' => 'i',
            '/Ĵ/' => 'J',
            '/ĵ/' => 'j',
            '/Ķ|Κ|К/' => 'K',
            '/ķ|κ|к/' => 'k',
            '/Ĺ|Ļ|Ľ|Ŀ|Ł|Λ|Л/' => 'L',
            '/ĺ|ļ|ľ|ŀ|ł|λ|л/' => 'l',
            '/М/' => 'M',
            '/м/' => 'm',
            '/Ñ|Ń|Ņ|Ň|Ν|Н/' => 'N',
            '/ñ|ń|ņ|ň|ŉ|ν|н/' => 'n',
            '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|Ο|Ό|Ω|Ώ|Ỏ|Ọ|Ồ|Ố|Ỗ|Ổ|Ộ|Ờ|Ớ|Ỡ|Ở|Ợ|О/' => 'O',
            '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|ο|ό|ω|ώ|ỏ|ọ|ồ|ố|ỗ|ổ|ộ|ờ|ớ|ỡ|ở|ợ|о/' => 'o',
            '/П/' => 'P',
            '/п/' => 'p',
            '/Ŕ|Ŗ|Ř|Ρ|Р/' => 'R',
            '/ŕ|ŗ|ř|ρ|р/' => 'r',
            '/Ś|Ŝ|Ş|Ș|Š|Σ|С/' => 'S',
            '/ś|ŝ|ş|ș|š|ſ|σ|ς|с/' => 's',
            '/Ț|Ţ|Ť|Ŧ|τ|Т/' => 'T',
            '/ț|ţ|ť|ŧ|т/' => 't',
            '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|Ũ|Ủ|Ụ|Ừ|Ứ|Ữ|Ử|Ự|У/' => 'U',
            '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|υ|ύ|ϋ|ủ|ụ|ừ|ứ|ữ|ử|ự|у/' => 'u',
            '/Ý|Ÿ|Ŷ|Υ|Ύ|Ϋ|Ỳ|Ỹ|Ỷ|Ỵ/' => 'Y',
            '/ý|ÿ|ŷ|ỳ|ỹ|ỷ|ỵ/' => 'y',
            '/В/' => 'V',
            '/в/' => 'v',
            '/Ŵ/' => 'W',
            '/ŵ/' => 'w',
            '/Ź|Ż|Ž|Ζ|З/' => 'Z',
            '/ź|ż|ž|ζ|з/' => 'z',
            '/Æ|Ǽ/' => 'AE',
            '/ß/'=> 'ss',
            '/Ĳ/' => 'IJ',
            '/ĳ/' => 'ij',
            '/Œ/' => 'OE',
            '/ƒ/' => 'f',
            '/ξ/' => 'ks',
            '/π/' => 'p',
            '/β/' => 'v',
            '/μ/' => 'm',
            '/ψ/' => 'ps',
            '/Ж/' => 'Zh',
            '/ж/' => 'zh',
            '/Х/' => 'Kh',
            '/х/' => 'kh',
            '/Ц/' => 'Tc',
            '/ц/' => 'tc',
            '/Ч/' => 'Ch',
            '/ч/' => 'ch',
            '/Ы/' => 'Y',
            '/ы/' => 'y',
            '/Ш/' => 'Sh',
            '/ш/' => 'sh',
            '/Щ/' => 'Shch',
            '/щ/' => 'shch',
            '/Ю/' => 'Iu',
            '/ю/' => 'iu',
            '/Я/' => 'Ia',
            '/я/' => 'ia',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Seo\Slug' => function ($sm) {
                $config = $sm->get('Configuration');
                if (! isset($config['seo_url'])) {
                    throw new \Exception('Configuration of Url not set.');
                }
                $service = new \Seo\Service\Slug($config['seo_url']);
                return $service;
            },
            'piwik' => function($sm){
                $service = new \Seo\Service\Piwik($sm);
                return $service;
            }
        ),
    ),
);
