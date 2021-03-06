<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog product form gallery content
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method \Magento\Framework\Data\Form\Element\AbstractElement getElement()
 */
namespace Cloudinary\Cloudinary\Block\Adminhtml\Product\Helper\Form\Gallery;

use Cloudinary\Cloudinary\Helper\MediaLibraryHelper;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\Json\EncoderInterface;

/**
 * Block for gallery content.
 */
class Content extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content
{
    /**
     * @var string
     */
    protected $_template = 'Cloudinary_Cloudinary::catalog/product/helper/gallery.phtml';

    /**
     * MediaLibraryHelper
     * @var array|null
     */
    protected $mediaLibraryHelper;

    /**
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param Config $mediaConfig
     * @param MediaLibraryHelper $mediaLibraryHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Config $mediaConfig,
        MediaLibraryHelper $mediaLibraryHelper,
        array $data = []
    ) {
        parent::__construct($context, $jsonEncoder, $mediaConfig, $data);
        $this->mediaLibraryHelper = $mediaLibraryHelper;
    }

    /**
     * Get Cloudinary media library widget options
     *
     * @param bool $multiple Allow multiple
     * @param bool $refresh Refresh options
     * @return string
     */
    public function getCloudinaryMediaLibraryWidgetOptions($multiple = true, $refresh = false)
    {
        if (!($cloudinaryMLoptions = $this->mediaLibraryHelper->getCloudinaryMLOptions($multiple, $refresh))) {
            return null;
        }

        try {
            //Try to add session param on Magento versions prior to 2.3.5
            $imageUploadUrl = $this->_urlBuilder->addSessionParam()->getUrl('cloudinary/ajax/retrieveImage');
        } catch (\Exception $e) {
            //Catch deprecation error on Magento 2.3.5 and above
            $imageUploadUrl = $this->_urlBuilder->getUrl('cloudinary/ajax/retrieveImage');
        }

        return $this->_jsonEncoder->encode(
            [
            'htmlId' => $this->getHtmlId(),
            'cldMLid' => 'product_gallery_' . $this->getHtmlId(),
            'imageUploaderUrl' => $imageUploadUrl,
            'triggerSelector' => '#media_gallery_content',
            'triggerEvent' => 'addItem',
            'useDerived' => false,
            'addTmpExtension' => true,
            'cloudinaryMLoptions' => $cloudinaryMLoptions,
            'cloudinaryMLshowOptions' => $this->mediaLibraryHelper->getCloudinaryMLshowOptions(null),
            ]
        );
    }

    /**
     * Escape a string for the HTML attribute context
     *
     * @param string $string
     * @param boolean $escapeSingleQuote
     * @return string
     */
    public function escapeHtmlAttr($string, $escapeSingleQuote = true)
    {
        if (method_exists($this->_escaper, 'escapeHtmlAttr')) {
            return $this->_escaper->escapeHtmlAttr($string, $escapeSingleQuote);
        }
        if ($escapeSingleQuote) {
            $escaper = new \Zend\Escaper\Escaper();
            return $escaper->escapeHtmlAttr((string) $string);
        }
        return htmlspecialchars((string)$string, ENT_COMPAT, 'UTF-8', false);
    }
}
