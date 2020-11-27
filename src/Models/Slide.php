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
use gorriecoe\LinkField\LinkField;

use NSWDPC\Elemental\Models\Slider\ElementSlider;

/**
 * Images in an ElementSlider
 */
class Slide extends DataObject {

    private static $table_name = 'Slide';

    private static $versioned_gridfield_extensions = true;

    private static $singular_name = 'Slide';
    private static $plural_name = 'Slides';

    private static $default_sort = 'Sort';

    private static $allowed_file_types = ["jpg","jpeg","gif","png","webp"];
    private static $default_thumb_width = 128;
    private static $default_thumb_height = 96;

    private static $db = [
        'Title' => 'Varchar(255)',
        'Sort' => 'Int',
        'Width' => 'Int',
        'Height' => 'Int'
    ];

    private static $has_one = [
        'Image' => Image::class,
        'Link'  => Link::class,
        'Parent' => ElementSlider::class,
    ];

    private static $summary_fields = [
        'Image.CMSThumbnail' => 'Image',
        'Title' => 'Title',
    ];

    private static $owns = [
        'Image'
    ];

    private static $extensions = [
        Versioned::class
    ];

    public function getThumbWidth() {
        $width = $this->Width;
        if($width <= 0) {
            $width = $this->config()->get('default_thumb_width');
        }
        return $width;
    }

    public function getThumbHeight() {
        $height = $this->Height;
        if($height <= 0) {
            $height = $this->config()->get('default_thumb_height');
        }
        return $height;
    }

    public function getAllowedFileTypes() {
        $types = $this->config()->get('allowed_file_types');
        if(empty($types)) {
            $types = ["jpg","jpeg","gif","png","webp"];
        }
        $types = array_unique($types);
        return $types;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->Width = $this->getThumbWidth();
        $this->Height = $this->getThumbHeight();
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->removeByName(['LinkID', 'ParentID', 'Sort']);

        $fields->addFieldsToTab(
            'Root.Main', [
                LinkField::create('Link', 'Link', $this->owner),
                NumericField::create(
                    'Width',
                    _t(
                        __CLASS__ . 'WIDTH', 'Thumbnail width'
                    )
                ),
                NumericField::create(
                    'Height',
                    _t(
                        __CLASS__ . 'WIDTH', 'Thumbnail height'
                    )
                ),
                UploadField::create(
                    'Image',
                    _t(
                        __CLASS__ . '.SLIDE_IMAGE',
                        'Image'
                    )
                )->setFolderName('sliders/' . $this->ID)
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

    public function getMultiRecordEditingTitle() {
        return $this->singular_name();
    }

    public function forTemplate() {
        return $this->renderWith([$this->class, __CLASS__]);
    }
}
