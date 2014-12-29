<?php namespace Foote\Ginny\Map;
/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ICanBoogie\Inflector;

/**
 *
 * The basic idea of this class is to allow for easy, flexible inflection of the item inside
 * templates. It is used as a basis for other "Base" classes in this directory.
 *
 * For example, for a Model called "PaymentMethod", I can easily pull off the following inflections, as follows:
 *
 * In my controller, I set: ['model' => BaseItem $paymentMethod]
 *
 * Then in my Twig template:
 *
 * {{ model }} -> PaymentMethod
 * {{ model.name }} -> PaymentMethod
 * {{ model.single }} -> PaymentMethod
 * {{ model.plural }} -> PaymentMethods
 * {{ model.camel }} -> PaymentMethod
 * {{ model.camels }} -> PaymentMethods
 * {{ model.variable }} -> paymentMethod
 * {{ model.variables }} -> paymentMethods
 * {{ model.snake }} -> payment_method
 * {{ model.snakes }} -> payment_methods
 * {{ model.getter }} -> getPaymentMethod
 * {{ model.getters }} -> getPaymentMethods
 * {{ model.setters }} -> setPaymentMethod
 * {{ model.setters }} -> setPaymentMethods
 * {{ model.adder }} -> addPaymentMethods
 * {{ model.remover }} -> removePaymentMethods
 * {{ model.human }} -> Payment method
 * {{ model.title }} -> Payment Method
 * {{ model.low }} -> paymentmethod
 * {{ model.up }} -> PAYMENTMETHOD
 *
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BaseItem
{

    /**
     * @var
     */
    public $name;

    /**
     * @param $name
     */
    public function __construct($name, $params = [])
    {

        $bits = explode('\\', $name);

        $this->name = end($bits);

        foreach($params as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @param $name
     */
    public static function create($name, $params = [])
    {
        $item = new static($name, $params);

        return $item;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Magic method to return $this->plural when $this->plural() is invoked
     *
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return $this->$name();
        }

        return NULL;
    }

    /**
     * @return string
     */
    public function plural()
    {
        return Inflector::get()->pluralize($this->name);
    }

    /**
     * @return string
     */
    public function single()
    {
        return Inflector::get()->singularize($this->name);
    }

    /**
     * @return string
     */
    public function variable()
    {
        return Inflector::get()->camelize($this->name, true);
    }

    /**
     * @return string
     */
    public function variables()
    {
        return Inflector::get()->camelize($this->plural(), true);
    }

    /**
     * @return string
     */
    public function camel()
    {
        return Inflector::get()->camelize($this->name);
    }

    /**
     * @return string
     */
    public function camels()
    {
        return Inflector::get()->camelize($this->plural());
    }

    /**
     * @return string
     */
    public function human()
    {
        return Inflector::get()->humanize($this->snake());
    }

    /**
     * @return string
     */
    public function title()
    {
        return Inflector::get()->titleize($this->name);
    }

    /**
     * @return string
     */
    public function snake()
    {
        return Inflector::get()->underscore($this->name);
    }

    /**
     * @return string
     */
    public function snakes()
    {
        return Inflector::get()->underscore($this->plural());
    }

    /**
     * @return string
     */
    public function low()
    {
        return strtolower($this->name);
    }

    /**
     * @return string
     */
    public function up()
    {
        return strtoupper($this->name);
    }

    /**
     * @return string
     */
    public function getter()
    {
        return 'get' . $this->camel();
    }

    /**
     * @return string
     */
    public function getters()
    {
        return 'get' . $this->camels();
    }

    /**
     * @return string
     */
    public function setter()
    {
        return 'set' . $this->camel();
    }

    /**
     * @return string
     */
    public function setters()
    {
        return 'set' . $this->camels();
    }

    /**
     * @return string
     */
    public function adder()
    {
        return 'add' . $this->camel();
    }

    /**
     * @return string
     */
    public function remover()
    {
        return 'remove' . $this->camel();
    }

    /**
     * hook to dump contents for debug. output is intended for shell.
     *
     * @return array()
     */
    public function dump() {

    }

    /**
     * hook to make any necessary updates
     */
    public function update() {

    }

    /**
     * hook to validate item
     *
     * @throws \Foote\Ginny\Exception\GinnyMapException
     */
    public function validate() {

    }

}