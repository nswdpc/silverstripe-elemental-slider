<?php

namespace NSWDPC\Elemental\Models\Slider;

use SilverStripe\Versioned\Versioned;
use SilverStripe\ORM\DataObject;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextareaField;
use gorriecoe\Link\Models\Link;
use gorriecoe\LinkField\LinkField;
use SilverStripe\LinkField\Models\Link as CoreLink;
use SilverStripe\LinkField\Form\LinkField as CoreLinkField;

/**
 * Images in an ElementSlider
 * @property string $Title
 * @property ?string $Content
 * @property int $Sort
 * @property int $Width
 * @property int $Height
 * @property int $ImageID
 * @property int $LinkID
 * @property int $ParentID
 * @method \SilverStripe\Assets\Image Image()
 * @method \gorriecoe\Link\Models\Link Link()
 * @method \SilverStripe\LinkField\Models\Link CoreLink()
 * @property int $CoreLinkID
 * @method \NSWDPC\Elemental\Models\Slider\ElementSlider Parent()
 * @mixin \SilverStripe\Versioned\Versioned
 */
class Slide extends DataObject
{
    /**
     * @inheritdoc
     */
    private static string $table_name = 'Slide';

    /**
     * @inheritdoc
     */
    private static bool $versioned_gridfield_extensions = true;

    /**
     * @inheritdoc
     */
    private static string $singular_name = 'Slide';

    /**
     * @inheritdoc
     */
    private static string $plural_name = 'Slides';

    /**
     * @inheritdoc
     */
    private static string $default_sort = 'Sort';

    /**
     * @inheritdoc
     */
    private static array $allowed_file_types = ["jpg","jpeg","gif","png","webp"];

    /**
     * @inheritdoc
     */
    private static int $default_thumb_width = 128;

    /**
     * @inheritdoc
     */
    private static int $default_thumb_height = 96;

    /**
     * @inheritdoc
     */
    private static array $db = [
        'Title' => 'Varchar(255)',
        'Content' => 'Text',
        'Sort' => 'Int',
        'Width' => 'Int',
        'Height' => 'Int'
    ];

    /**
     * @inheritdoc
     */
    private static array $has_one = [
        'Image' => Image::class,
        'Link'  => Link::class,
        'CoreLink' => CoreLink::class,
        'Parent' => ElementSlider::class,
    ];

    /**
     * @inheritdoc
     */
    private static array $summary_fields = [
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
    private static array $searchable_fields = [
        'Title' => 'PartialMatchFilter',
        'Content' => 'PartialMatchFilter'
    ];

    /**
     * @inheritdoc
     */
    private static array $owns = [
        'Image',
        'CoreLink'
    ];

    private static array $cascade_deletes = [
        'CoreLink'
    ];

    private static array $cascade_duplicates = [
        'CoreLink'
    ];

    /**
     * @inheritdoc
     */
    private static array $extensions = [
        Versioned::class
    ];

    /**
     * template method
     */
    public function ThumbWidth(): int
    {
        return $this->getThumbWidth();
    }

    /**
     * Return thumbnail width value
     */
    public function getThumbWidth(): int
    {
        $width = $this->Width;
        if ($width <= 0) {
            $width = $this->config()->get('default_thumb_width');
        }

        return (int) $width;
    }

    /**
     * template method
     */
    public function ThumbHeight(): int
    {
        return $this->getThumbHeight();
    }

    /**
     * Return thumbnail height value
     */
    public function getThumbHeight(): int
    {
        $height = $this->Height;
        if ($height <= 0) {
            $height = $this->config()->get('default_thumb_height');
        }

        return (int) $height;
    }

    /**
     * Return allowed file types
     */
    public function getAllowedFileTypes(): array
    {
        $types = $this->config()->get('allowed_file_types');
        if (empty($types)) {
            $types = ["jpg","jpeg","gif","png","webp"];
        }

        return array_unique($types);
    }

    /**
     * @inheritdoc
     */
    #[\Override]
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->Width = $this->getThumbWidth();
        $this->Height = $this->getThumbHeight();
    }

    /**
     * @inheritdoc
     */
    #[\Override]
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName(['LinkID', 'Link', 'CoreLink', 'ParentID', 'Sort']);

        $folderName = $this->Title . "-" . $this->ID;

        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextareaField::create(
                    'Content',
                    _t(
                        self::class . 'CONTENT',
                        'Content'
                    )
                ),
                $this->getLinkField(),
                NumericField::create(
                    'Width',
                    _t(
                        self::class . 'THUMBNAIL_WIDTH',
                        'Thumbnail width'
                    ),
                    static::config()->get('default_thumb_width')
                )->setHtml5(true),
                NumericField::create(
                    'Height',
                    _t(
                        self::class . 'THUMBNAIL_HEIGHT',
                        'Thumbnail height'
                    ),
                    static::config()->get('default_thumb_height')
                )->setHtml5(true),
                UploadField::create(
                    'Image',
                    _t(
                        self::class . '.SLIDE_IMAGE',
                        'Image'
                    )
                )->setFolderName('sliders/' . $folderName)
                ->setAllowedExtensions($this->getAllowedFileTypes())
                ->setDescription(
                    sprintf(_t(
                        self::class . 'ALLOWED_FILE_TYPES',
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
    public function getMultiRecordEditingTitle()
    {
        return $this->singular_name();
    }

    /**
     * Render the slide into a template
     */
    #[\Override]
    public function forTemplate(): string
    {
        return $this->renderWith(self::class);
    }

    /**
     * Return the field used to render the link
     * based on migration state
     */
    protected function getLinkField(): CoreLinkField|LinkField
    {
        $link = $this->getDerivedLink();
        if ($link instanceof CoreLink) {
            return CoreLinkField::create('CoreLink', _t(self::class . 'LINK', 'Link'));
        }

        return LinkField::create('Link', _t(self::class . 'LINK', 'Link'), $this);
    }

    /**
     * Template method
     */
    public function DerivedLink(): CoreLink|Link
    {
        return $this->getDerivedLink();
    }

    /**
     * Returns the relevant link based on migration status
     */
    public function getDerivedLink(): CoreLink|Link
    {
        $coreLink = $this->CoreLink();
        /** @var Link $legacyLink */
        $legacyLink = $this->getComponent('Link');
        if ($coreLink->isInDB() || !$legacyLink->isInDB()) {
            return $coreLink;
        }

        return $legacyLink;
    }
}
