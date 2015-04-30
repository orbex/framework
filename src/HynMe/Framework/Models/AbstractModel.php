<?php namespace HynMe\Framework\Models;

use ReflectionClass;
use Illuminate\Database\Eloquent\Model;

class AbstractModel extends Model
{
    /**
     * @return string
     */
    public function getConnectionName()
    {
        // overrules connection name in case of using multi tenancy
        if(env('HYN_MULTI_TENANCY_HOSTNAME') && is_null($this->connection))
            return 'tenant';
        // fallback to parent method
        return parent::getConnectionName();
    }

    /**
     * Loads class name reflection information
     * @return array
     */
    public function getClassNameReflectionsAttribute()
    {
        $reflect = new ReflectionClass($this);

        $ret = [];

        foreach(['inNamespace', 'getName', 'getNamespaceName', 'getShortName'] as $method)
        {
            $label = str_replace('get', null, $method);
            $label = snake_case($label);
            $ret[$label] = $reflect->{$method}();
        }

        $ret['vendor'] = head(explode('\\', $ret['namespace_name']));
        $ret['package'] = array_get(explode('\\', $ret['namespace_name']),1);

        return $ret;
    }
    /**
     * Complete namespaced class name of called class
     * @return string
     */
    public function getClassNameAttribute()
    {
        return get_called_class($this);
    }

    /**
     * @return string
     */
    public function getEasyClassNameAttribute()
    {
        return snake_case($this->classNameReflections['short_name']);
    }
}