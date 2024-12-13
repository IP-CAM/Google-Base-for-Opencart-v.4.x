<?php
namespace Opencart\Catalog\Controller\Extension\PSGoogleBase\Feed;
/**
 * Class PSGoogleBase
 *
 * @package Opencart\Catalog\Controller\Extension\PSGoogleBase\Feed
 */
class PSGoogleBase extends \Opencart\System\Engine\Controller
{
    /**
     * Generates and outputs an XML feed for Google Merchant using the configured product details.
     *
     * This method checks the feed status and validates user authentication
     * before loading product data and constructing an XML document. The XML
     * feed includes essential product details like title, description,
     * price, availability, and categorization as required by Google Merchant.
     *
     * It supports basic authentication for access control and returns a
     * 401 Unauthorized status if the credentials are invalid. The generated
     * XML feed adheres to the Google Merchant feed specifications.
     *
     * @return void
     */
    public function index(): void
    {
        if (!$this->config->get('feed_ps_google_base_status')) {
            return;
        }

        $this->load->model('setting/setting');

        $config = $this->model_setting_setting->getSetting('feed_ps_google_base', $this->config->get('config_store_id'));

        $additional_images = isset($config['feed_ps_google_base_additional_images']) ? (bool) $config['feed_ps_google_base_additional_images'] : false;
        $skip_out_of_stock = isset($config['feed_ps_google_base_skip_out_of_stock']) ? (bool) $config['feed_ps_google_base_skip_out_of_stock'] : false;
        $base_login = isset($config['feed_ps_google_base_login']) ? $config['feed_ps_google_base_login'] : '';
        $base_password = isset($config['feed_ps_google_base_password']) ? $config['feed_ps_google_base_password'] : '';
        $base_tax_status = isset($config['feed_ps_google_base_tax']) ? (bool) $config['feed_ps_google_base_tax'] : false;
        $base_tax_definitions = isset($config['feed_ps_google_base_taxes']) ? (array) $config['feed_ps_google_base_taxes'] : [];

        if ($base_login && $base_password) {
            header('Cache-Control: no-cache, must-revalidate, max-age=0');

            if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
                header('WWW-Authenticate: Basic realm="ps_google_base"');
                header('HTTP/1.1 401 Unauthorized');
                echo 'Invalid credentials';
                exit;
            } else {
                if ($_SERVER['PHP_AUTH_USER'] !== $base_login || $_SERVER['PHP_AUTH_PW'] !== $base_password) {
                    header('WWW-Authenticate: Basic realm="ps_google_base"');
                    header('HTTP/1.1 401 Unauthorized');
                    echo 'Invalid credentials';
                    exit;
                }
            }
        }


        $this->load->model('extension/ps_google_base/feed/ps_google_base');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        $language = $this->config->get('config_language');
        $language_id = (int) $this->config->get('config_language_id');

        if (isset($this->request->get['language']) && isset($languages[$this->request->get['language']])) {
            $cur_language = $languages[$this->request->get['language']];

            $language = $cur_language['code'];
            $language_id = $cur_language['language_id'];
        }


        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');

        // Start <rss> element
        $xml->startElement('rss');
        $xml->writeAttribute('version', '2.0');
        $xml->writeAttribute('xmlns:g', 'http://base.google.com/ns/1.0');

        // Start <channel> element
        $xml->startElement('channel');

        // Add channel metadata
        $xml->writeElement('title', $this->config->get('config_name'));
        $xml->writeElement('description', $this->config->get('config_meta_description'));

        $link = $this->url->link('common/home', 'language=' . $language);
        $xml->writeElement('link', str_replace('&amp;', '&', $link));

        $taxes = [];

        if (is_array($base_tax_definitions)) {
            foreach ($base_tax_definitions as $base_tax_definition) {
                $tax_rate_info = $this->model_extension_ps_google_base_feed_ps_google_base->getTaxRate($base_tax_definition['tax_rate_id']);

                if ($tax_rate_info) {
                    $taxes[] = [
                        'country_id' => $base_tax_definition['country_id'],
                        'region' => $base_tax_definition['region'],
                        'tax_rate' => $tax_rate_info['rate'],
                        'tax_ship' => $base_tax_definition['tax_ship'],
                    ];
                }
            }
        }

        $product_data = [];

        $google_base_categories = $this->model_extension_ps_google_base_feed_ps_google_base->getCategories();

        foreach ($google_base_categories as $google_base_category) {
            $filter_data = [
                'filter_category_id' => $google_base_category['category_id'],
                'filter_filter' => false
            ];

            $products = $this->model_catalog_product->getProducts($filter_data);

            foreach ($products as $product) {
                if (!in_array($product['product_id'], $product_data) && $product['description']) {
                    $product_data[] = $product['product_id'];

                    if (0 === (int) $product['status']) {
                        continue;
                    }

                    if ($skip_out_of_stock && 0 === (int) $product['quantity']) {
                        continue;
                    }

                    $xml->startElement('item');

                    // Add product details with CDATA for name, description, manufacturer
                    $xml->startElement('title');
                    $xml->writeCData(html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8'));
                    $xml->endElement();

                    $product_link = $this->url->link('product/product', 'language=' . $language . '&product_id=' . $product['product_id']);
                    $xml->writeElement('link', str_replace('&amp;', '&', $product_link));

                    $xml->startElement('description');
                    $xml->writeCData($this->normalizeDescription($product['description']));
                    $xml->endElement();

                    if (isset($product['manufacturer'])) {
                        $xml->startElement('g:brand');
                        $xml->writeCData(html_entity_decode($product['manufacturer'], ENT_QUOTES, 'UTF-8'));
                        $xml->endElement();
                    }

                    // Static values and conditions
                    $xml->writeElement('g:condition', 'new');
                    $xml->writeElement('g:id', $product['product_id']);

                    // Image link
                    $image_link = $product['image'] ? $this->model_tool_image->resize(html_entity_decode($product['image'], ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')) : null;

                    if ($image_link) {
                        $xml->startElement('g:image_link');
                        $xml->writeCData($image_link);
                        $xml->endElement();
                    }

                    if ($additional_images && $product_images = $this->model_catalog_product->getImages($product['product_id'])) {
                        foreach ($product_images as $product_image) {
                            $image_link = $product_image['image'] ? $this->model_tool_image->resize(html_entity_decode($product_image['image'], ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')) : null;

                            if ($image_link) {
                                $xml->startElement('g:additional_image_link');
                                $xml->writeCData($image_link);
                                $xml->endElement();
                            }
                        }
                    }

                    // Model number
                    $xml->writeElement('g:model_number', $product['model']);

                    // MPN, UPC, and EAN with CDATA where applicable
                    if ($product['mpn']) {
                        $xml->startElement('g:mpn');
                        $xml->writeCData($product['mpn']);
                        $xml->endElement();
                    } else {
                        $xml->writeElement('g:identifier_exists', 'false');
                    }

                    if ($product['upc']) {
                        $xml->writeElement('g:upc', $product['upc']);
                    }

                    if ($product['ean']) {
                        $xml->writeElement('g:ean', $product['ean']);
                    }

                    // Price (handling special price if available)
                    if ($base_tax_status) {
                        $formatted_price = $product['price'];
                    } else {
                        $formatted_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
                    }

                    $xml->writeElement('g:price', $this->currency->format($formatted_price, $this->config->get('config_currency'), 0, false) . ' ' . $this->config->get('config_currency'));

                    if ((float) $product['special']) {
                        if ($base_tax_status) {
                            $formatted_price = $product['special'];
                        } else {
                            $formatted_price = $this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax'));
                        }

                        $xml->writeElement('g:sale_price', $this->currency->format($formatted_price, $this->config->get('config_currency'), 0, false) . ' ' . $this->config->get('config_currency'));

                        $sale_dates = $this->model_extension_ps_google_base_feed_ps_google_base->getSpecialPriceDatesByProductId($product['product_id']);

                        if (
                            isset($sale_dates['date_start'], $sale_dates['date_end']) &&
                            $sale_dates['date_start'] !== '0000-00-00' &&
                            $sale_dates['date_end'] !== '0000-00-00'
                        ) {
                            $sale_start_date = date('Y-m-d\TH:iO', strtotime($sale_dates['date_start'] . ' 00:00:00'));
                            $sale_end_date = date('Y-m-d\TH:iO', strtotime($sale_dates['date_end'] . ' 23:59:59'));

                            $xml->writeElement('g:sale_price_effective_date', $sale_start_date . '/' . $sale_end_date);
                        }
                    }

                    #region <g:tax> element
                    if ($base_tax_status) {
                        foreach ($taxes as $tax) {
                            $xml->startElement('g:tax');

                            $xml->writeElement('g:country', $tax['country_id']);
                            $xml->writeElement('g:region', $tax['region']);
                            $xml->writeElement('g:rate', $tax['tax_rate']);
                            $xml->writeElement('g:tax_ship', $tax['tax_ship'] ? 'yes' : 'no');

                            $xml->endElement();
                        }
                    }
                    #endregion <g:tax> element

                    // Google product category
                    $xml->writeElement('g:google_product_category', $google_base_category['google_base_category']);

                    // Categories and product type with CDATA
                    $categories = $this->model_catalog_product->getCategories($product['product_id']);

                    foreach ($categories as $category) {
                        $path = $this->getPath($category['category_id']);

                        if ($path) {
                            $string = '';

                            foreach (explode('_', $path) as $path_id) {
                                $category_info = $this->model_catalog_category->getCategory($path_id);

                                if ($category_info) {
                                    if (!$string) {
                                        $string = $category_info['name'];
                                    } else {
                                        $string .= ' &gt; ' . $category_info['name'];
                                    }
                                }
                            }

                            $xml->startElement('g:product_type');
                            $xml->writeCData($string);
                            $xml->endElement();
                        }
                    }


                    // Quantity and weight
                    $xml->writeElement('g:quantity', $product['quantity']);
                    $xml->writeElement('g:weight', $this->weight->format($product['weight'], $product['weight_class_id']));

                    // Availability with CDATA
                    $xml->startElement('g:availability');
                    $xml->writeCData($product['quantity'] ? 'in stock' : 'out of stock');
                    $xml->endElement();

                    // End <item> element
                    $xml->endElement();
                }
            }
        }

        // Close <channel> and <rss> elements
        $xml->endElement(); // End <channel>
        $xml->endElement(); // End <rss>

        $xml->endDocument();

        $this->response->addHeader('Content-Type: application/xml');
        $this->response->setOutput($xml->outputMemory());
    }

    /**
     * Recursively retrieves the path of a category based on its parent ID.
     *
     * This method constructs the full path of a category by concatenating the
     * category IDs from the specified category to its root parent. The path is
     * built in reverse order, starting from the specified category and moving
     * up to the top-level parent category.
     *
     * @param int $parent_id The ID of the parent category to retrieve the path for.
     * @param string $current_path (optional) The current path being constructed.
     *                             Defaults to an empty string. This is used in the
     *                             recursive calls to build the full path.
     *
     * @return string Returns the constructed path of category IDs, separated by underscores.
     *                If the category does not exist or if there is no valid path,
     *                it returns an empty string.
     */
    protected function getPath($parent_id, $current_path = ''): string
    {
        $category_info = $this->model_catalog_category->getCategory($parent_id);

        if ($category_info) {
            if (!$current_path) {
                $new_path = $category_info['category_id'];
            } else {
                $new_path = $category_info['category_id'] . '_' . $current_path;
            }

            $path = $this->getPath($category_info['parent_id'], $new_path);

            if ($path) {
                return $path;
            } else {
                return $new_path;
            }
        }

        return '';
    }

    /**
     * Normalizes the product description by decoding HTML entities,
     * stripping unallowed HTML tags, normalizing whitespace,
     * trimming the text, and ensuring it does not exceed the maximum length.
     *
     * This method processes the input description to make it safe for
     * use in a Google Merchant feed by allowing only specific HTML tags
     * and applying various cleaning operations. If the resulting
     * description exceeds 5000 characters, it is truncated to this limit.
     *
     * @param string $description The raw product description to normalize.
     *
     * @return string Returns the cleaned and normalized product description,
     *                with allowed HTML tags, normalized whitespace, and a
     *                maximum length of 5000 characters.
     */
    private function normalizeDescription(string $description): string
    {
        // Decode HTML entities
        $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');

        // Allowable HTML tags
        $allowed_tags = '<b><strong><i><em><u><br><ul><li><ol><p>';
        $description = strip_tags($description, $allowed_tags);

        // Normalize whitespace
        $description = preg_replace(['/[\r\n\t]+/', '/\s+/'], [' ', ' '], $description);

        // Trim the description
        $description = trim($description);

        // Check for maximum length
        if ($this->_strlen($description) > 5000) {
            $description = $this->_substr($description, 0, 5000); // Truncate to 5000 characters
        }

        return $description;
    }

    /**
     * Get the length of a string while ensuring compatibility across OpenCart versions.
     *
     * This method returns the length of the provided string. It utilizes different
     * string length functions based on the OpenCart version being used to ensure
     * accurate handling of UTF-8 characters.
     *
     * - For OpenCart versions before 4.0.1.0, it uses `utf8_strlen()`.
     * - For OpenCart versions from 4.0.1.0 up to (but not including) 4.0.2.0,
     *   it uses `\Opencart\System\Helper\Utf8\strlen()`.
     * - For OpenCart version 4.0.2.0 and above, it uses `oc_strlen()`.
     *
     * @param string $string The input string whose length is to be calculated.
     *
     * @return int The length of the input string.
     */
    private function _strlen(string $string): int
    {
        if (version_compare(VERSION, '4.0.1.0', '<')) { // OpenCart versions before 4.0.1.0
            return utf8_strlen($string);
        } elseif (version_compare(VERSION, '4.0.2.0', '<')) { // OpenCart version 4.0.1.0 up to, but not including, 4.0.2.0
            return \Opencart\System\Helper\Utf8\strlen($string);
        }

        return oc_strlen($string); // OpenCart version 4.0.2.0 and above
    }

    /**
     * Get a substring from a string while ensuring compatibility across OpenCart versions.
     *
     * This method returns a portion of the provided string. It utilizes different
     * substring functions based on the OpenCart version being used to ensure
     * accurate handling of UTF-8 characters.
     *
     * - For OpenCart versions before 4.0.1.0, it uses `utf8_substr()`.
     * - For OpenCart versions from 4.0.1.0 up to (but not including) 4.0.2.0,
     *   it uses `\Opencart\System\Helper\Utf8\substr()`.
     * - For OpenCart version 4.0.2.0 and above, it uses `substr()`.
     *
     * @param string $value The input string from which to extract the substring.
     * @param int $start The starting position of the substring.
     * @param int|null $length The length of the substring (optional).
     *
     * @return string The extracted substring.
     */
    private function _substr(string $value, int $start, ?int $length = null): string
    {
        if (version_compare(VERSION, '4.0.1.0', '<')) { // OpenCart versions before 4.0.1.0
            return utf8_substr($value, $start, $length);
        } elseif (version_compare(VERSION, '4.0.2.0', '<')) { // OpenCart version 4.0.1.0 up to, but not including, 4.0.2.0
            return \Opencart\System\Helper\Utf8\substr($value, $start, $length);
        }

        return substr($value, $start, $length); // OpenCart version 4.0.2.0 and above
    }

}
