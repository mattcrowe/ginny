<?php namespace Foote\Ginny\Map;
/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Exception\GinnyMapException;

/**
 * BaseField is added to BaseModel
 *
 * @see BaseModel
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BaseField extends BaseItem
{

    public $name;

    public $model;

    public $type;
    public $size;
    public $default;
    public $required = true;
    public $unique = false;
    public $primary = false;
    public $autoIncrement = false;
    public $owner = false;

    public $_sensitive = ['hash', 'pass', 'password', 'salt', 'secret', 'signature', 'token'];

    public static function create($name, $params=[]) {

        $field = new static($name, $params);

        return $field;
    }

    /**
     * helper function to identify which fields to skip for index views, etc
     *
     * @return bool
     */
    public function sensitive()
    {
        if (in_array($this->name, $this->_sensitive)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $s = $this->name . ': ';

        switch($this->type) {
            case 'string':
                $s .= sprintf('%s(%s)', $this->type, $this->size);
                break;
            case 'integer':
                $s .= sprintf('%s(%s)', $this->type, $this->size);
                break;
            default:
                $s .= $this->type;
                break;
        }

        $s .= $this->required ? '' : ', NULLABLE';

        $s .= !$this->default ? '' : sprintf(", DEFAULT='%s'", $this->default);

        $s .= !$this->unique ? '' : ', UNIQUE';

        $s .= !$this->primary ? '' : ', PRIMARY';

        $s .= !$this->autoIncrement ? '' : ', AUTOINC';

        return $s;
    }

    /**
     * {@inheritdoc}
     */
    public function update()
    {
        if ($this->type == 'integer' && $this->size == 1) {
            $this->type = 'boolean';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate() {

        # GinnyMapException::400
        if (empty($this->type)) {
            throw new GinnyMapException(
                'BaseField::$type empty',
                400
            );
        }

        # GinnyMapException::401
        if (empty($this->model)) {
            throw new GinnyMapException(
                'BaseField::$model empty',
                401
            );
        }

    }

}