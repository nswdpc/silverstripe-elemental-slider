<?php
namespace NSWDPC\Elemental\Models\Slider;

use SilverStripe\Versioned\Versioned;
use SilverStripe\ORM\DataObject;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use gorriecoe\Link\Models\Link;
use NSWDPC\InlineLinker\InlineLinkCompositeField;

use NSWDPC\Elemental\Models\Slider\ElementSlider;

/**
 * Images in an ElementSlider
 */
class Slide extends DataObject {

    /**
     * @inheritdoc
     */
    private static $table_name = 'Slide';

    /**
     * @inheritdoc
     */
    private static $versioned_gridfield_extensions = true;

    /**
     * @inheritdoc
     */
    private static $singular_name = 'Slide';

    /**
     * @inheritdoc
     */
    private static $plural_name = 'Slides';

    /**
     * @inheritdoc
     */
    private static $default_sort = 'Sort';

    /**
     * @inheritdoc
     */
    private static $allowed_file_types = ["jpg","jpeg","gif","png","webp"];

    /**
     * @inheritdoc
     */
    private static $default_thumb_width = 128;

    /**
     * @inheritdoc
     */
    private static $default_thumb_height = 96;

    /**
     * @inheritdoc
     */
    private static $db = [
        'Title' => 'Varchar(255)',
        'Content' => 'Text',
        'Sort' => 'Int',
        'Width' => 'Int',
        'Height' => 'Int'
    ];

    /**
     * @inheritdoc
     */
    private static $has_one = [
        'Image' => Image::class,
        'Link'  => Link::class,
        'Parent' => ElementSlider::class,
    ];

    /**
     * @inheritdoc
     */
    private static $summary_fields = [
        'Image.CMSThumbnail' => 'Image',
        'Title' => 'Title',
        'Width' => 'Width',
        'Height' => 'Height',
        'Link.TypeLabel' => 'Link type',
        'Link.LinkURL' => 'Link URL'
    ];

    /**
     * @inheritdoc
     */
    private static $searchable_fields = [
        'Title' => 'PartialMatchFilter',
        'Content' => 'PartialMatchFilter'
    ];

    /**
     * @inheritdoc
     */
    private static $owns = [
        'Image'
    ];

    /**
     * @inheritdoc
     */
    private static $extensions = [
        Versioned::class
    ];

    /**
     * Return thumbnail width value
     */
    public function getThumbWidth() : int{
        $width = $this->Width;
        if($width <= 0) {
            $width = $this->config()->get('default_thumb_width');
        }
        return $width;
    }

    /**
     * Return thumbnail height value
     */
    public function getThumbHeight() : int {
        $height = $this->Height;
        if($height <= 0) {
            $height = $this->config()->get('default_thumb_height');
        }
        return $height;
    }

    /**
     * Return allowed file types
     */
    public function getAllowedFileTypes() : array {
        $types = $this->config()->get('allowed_file_types');
        if(empty($types)) {
            $types = ["jpg","jpeg","gif","png","webp"];
        }
        $types = array_unique($types);
        return $types;
    }

    /**
     * @inheritdoc
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->Width = $this->getThumbWidth();
        $this->Height = $this->getThumbHeight();
    }

    /**
     * @inheritdoc
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->removeByName(['LinkID', 'ParentID', 'Sort']);

        $folderName = $this->Title . "-" . $this->ID;

        $fields->addFieldsToTab(
            'Root.Main', [
                TextareaField::create(
                    'Content',
                    _t(
                        __CLASS__ . 'CONTENT', 'Content'
                    )
                ),
                InlineLinkCompositeField::create(
                    'Link',
                    _t(
                        __CLASS__ . 'LINK', 'Link'
                    ),
                    $this->owner
                ),
                NumericField::create(
                    'Width',
                    _t(
                        __CLASS__ . 'THUMBNAIL_WIDTH', 'Thumbnail width'
                    ),
                    static::config()->get('default_thumb_width')
                )->setHtml5(true),
                NumericField::create(
                    'Height',
                    _t(
                        __CLASS__ . 'THUMBNAIL_HEIGHT', 'Thumbnail height'
                    ),
                    static::config()->get('default_thumb_height')
                )->setHtml5(true),
                UploadField::create(
                    'Image',
                    _t(
                        __CLASS__ . '.SLIDE_IMAGE',
                        'Image'
                    )
                )->setFolderName('sliders/' . $folderName)
                ->setAllowedExtensions($this->getAllowedFileTypes())
                ->setDescription(
                    sprintf(_t(
                        __CLASS__ . 'ALLOWED_FILE_TYPES',
                        'Allowed file types: %s'
                    ), implode(",", $this->getAllowedFileTypes()))
                )
            ]
        );

        return $fields;
    }

    /**
     * For gridfield extensions
     */
    public function getMultiRecordEditingTitle() {
        return $this->singular_name();
    }

    /**
     * Render the slide int a template
     */
    public function forTemplate() {
        return $this->renderWith(__CLASS__);
    }
}
