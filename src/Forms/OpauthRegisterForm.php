<?php
namespace Silverstripe\Opauth\Forms;

use InvalidArgumentException;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Session;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;
use Silverstripe\Opauth\Validators\OpauthValidator;
use SilverStripe\Security\Member;
use SilverStripe\Control\Controller;

/**
 * OpauthRegisterForm
 * Presented to users whose OpauthIdentity object does not provide enough info.
 * This is triggered by the Member failing validation; you can modify this by
 * hooking in to the Member::validate() method via a DataExtension.
 * @author Will Morgan <@willmorgan>
 * @copyright Copyright (c) 2013, Better Brief LLP
 */
class OpauthRegisterForm extends Form
{

    protected $fields;

    protected $requiredFields;

    protected static $field_source;

    /**
     * @param Controller $controller
     * @param string $name
     * @param array|null $requiredFields
     */
    public function __construct($controller, $name, array $requiredFields = null)
    {
        if (isset($requiredFields)) {
            $this->requiredFields = $requiredFields;
        }
        parent::__construct($controller, $name, $this->getFields(), $this->getActions(), $this->getValidator());
        // Manually call extensions here as Object must first construct extensions
        $this->extend('updateFields', $this->fields);
        $this->extend('updateActions', $this->actions);
    }

    /**
     * setRequiredFields
     * Resets everything if the fields change
     */
    public function setRequiredFields($fields)
    {
        $this->requiredFields = $fields;
        $this->setValidator($this->getValidator());
        return $this;
    }

    /**
     * getFields
     * Picks only the required fields from the field source
     * and then presents them in a field set.
     * @return FieldList
     */
    public function getFields(): FieldList
    {
        $fields = $this->getFieldSource();
        $this->extend('updateFields', $fields);
        return $fields;
    }

    /**
     * Uses the field_source defined, or falls back to the Member's getCMSFields
     * @return FieldList
     */
    public function getFieldSource(): FieldList
    {
        if (is_callable(self::$field_source)) {
            $fields = call_user_func(self::$field_source, $this);
            if (!$fields instanceof FieldList) {
                throw new InvalidArgumentException('Field source must be callable and return a FieldList');
            }
            return $fields;
        }
        return new FieldList(singleton(Member::class)->getCMSFields()->dataFields());
    }

    /**
     * Set a callable as a data provider for the field source. Field names must
     * match those found on @param callable $sourceFn Source closure to use, accepts $this as param
     * @see Member so they can be filtered accordingly.
     *
     * Callable docs: http://php.net/manual/en/language.types.callable.php
     */
    public static function set_field_source($sourceFn)
    {
        if (!is_callable($sourceFn)) {
            throw new InvalidArgumentException('$sourceFn must be callable and return a FieldList');
        }
        self::$field_source = $sourceFn;
    }

    /**
     * Get actions
     * Points to a controller action
     * @return FieldList
     */
    public function getActions(): FieldList
    {
        $actions = new FieldList(array(
            new FormAction('doCompleteRegister', _t('OpauthRegisterForm.COMPLETE', 'Complete')),
        ));
        $this->extend('updateActions', $actions);
        return $actions;
    }

    /**
     * @return RequiredFields
     */
    public function getValidator(): RequiredFields
    {
        return Injector::inst()->create(OpauthValidator::class, $this->requiredFields);
    }

    /**
     * Populates the form somewhat intelligently
     * @param HTTPRequest|null $request Any request
     * @param Member|null $member Any member
     * @param array|null $required Any validation messages
     * @return $this
     */
    public function populateFromSources(HTTPRequest $request = null, Member $member = null, array $required = null)
    {
        $dataPath = "FormInfo.{$this->FormName()}.data";
        if (isset($member)) {
            $this->loadDataFrom($member);
        } else if (isset($request)) {
            $this->loadDataFrom($request->postVars());
        } // Hacky again :(
        else if (Controller::curr()->getRequest()->getSession()->get($dataPath)) {
            $this->loadDataFrom(Controller::curr()->getRequest()->getSession()->get($dataPath));
        } else if ($failover = $this->getSessionData()) {
            $this->loadDataFrom($failover);
        }
        if (!empty($required)) {
            $this->setRequiredFields($required);
        }
        return $this;
    }

    /**
     * Set failover data, so a user can refresh without losing his or her data
     * @param mixed $data Any type useable with $this->loadDataFrom
     */
    public function setSessionData($data): OpauthRegisterForm|static
    {
        Controller::curr()->getRequest()->getSession()->set($this->class . '.data', $data);
        return $this;
    }

    public function getSessionData()
    {
        return Controller::curr()->getRequest()->getSession()->get($this->class . '.data');
    }

    public function clearSessionData(): static
    {
        Controller::curr()->getRequest()->getSession()->clear($this->class . '.data');
        return $this;
    }

    /**
     * mockErrors
     * Uses a very nasty trick to dynamically create some required field errors
     */
    public function mockErrors()
    {
        $this->validate();
    }

}
