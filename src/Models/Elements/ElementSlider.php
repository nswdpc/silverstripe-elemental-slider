<?php
namespace NSWDPC\Elemental\Models\Slider;

use NSWDPC\Elemental\Models\Slider\Slide;
use DNADesign\Elemental\Models\ElementContent;
use gorriecoe\Link\Models\Link;
use NSWDPC\InlineLinker\InlineLinkCompositeField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * ElementSlider adds a content slider via a sortable upload field
 */
class ElementSlider extends ElementContent {

    /**
     * @inheritdoc
     */
    private static $icon = 'font-icon-picture';

    /**
     * @inheritdoc
     */
    private static $inline_editable = false;

    /**
     * @inheritdoc
     */
    private static $table_name = 'ElementSlider';

    /**
     * @inheritdoc
     */
    private static $title = 'Content slider';

    /**
     * @inheritdoc
     */
    private static $description = "Display one or more slides with optional hero";

    /**
     * @inheritdoc
     */
    private static $singular_name = 'Slider';

    /**
     * @inheritdoc
     */
    private static $plural_name = 'Sliders';

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Content slider');
    }

    /**
     * @inheritdoc
     */
    private static $db = [
        'HomepageHero' => 'Boolean'
    ];

    /**
     * @inheritdoc
     */
    private static $has_one = [
        'HeroLink' => Link::class
    ];

    /**
     * @inheritdoc
     */
    private static $has_many = [
        'Slides' => Slide::class,
    ];

    /**
     * @inheritdoc
     */
    private static $owns = [
        'Slides'
    ];

    /**
     * @inheritDoc
     */
    public function forTemplate($holder = true)
    {
        $this->addSliderRequirements();
        return parent::forTemplate($holder);
    }

    /**
     * Provides an extension method 'sliderRequirements' where Requirements can
     * be added to handle slider content
     */
    protected function addSliderRequirements() {
        $this->extend('sliderRequirements');
    }

    /**
     * @inheritdoc
     */
    public function getCMSFields()
    {
        $fields = parent::getCmsFields();
        $fields->removeByName(['HeroLinkID']);

        $fields->addFieldToTab(
            'Root.Main',
                CheckboxField::create(
                    'HomepageHero',
                    _t(
                        __CLASS__ . 'HOMEPAGE_HERO',
                        'The context for this slider is a \'hero\''
                    )
                )
        );

        $fields->addFieldToTab(
            'Root.Main',
                $this->getLinkField()
        );

        if ($this->isInDB()) {
            $field = GridField::create(
                'Slides',
                _t(
                    __CLASS__ . 'SLIDES', 'Slides'
                ),
                $this->Slides(),
                GridFieldConfig_RelationEditor::create()
            );
            $config = $field->getConfig();
            $config->addComponent(GridFieldOrderableRows::create('Sort'));
            $fields->addFieldToTab(
                'Root.Slides',
                $field
            );
        }

        return $fields;
    }

    /**
     * @inheritdoc
     */
    protected function getLinkField() {
        $field = InlineLinkCompositeField::create(
            'HeroLink',
            _t(
                __CLASS__ . '.LINK',
                'Link'
            ),
            $this
        );
        return $field;
    }

    /**
     * @inheritdoc
     */
    public function SortedSlides() {
        return $this->Slides()->Sort('Sort');
    }

}
