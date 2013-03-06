<?php
namespace Orlex;

trait ConfigurationSetterMapTrait {
    /**
     * @param array $config
     * @param bool $strict
     * @throws \InvalidArgumentException
     */
    public function processConfigurationArray(array $config, $strict = false) {
        foreach ($config as $key => $value) {
            $method = "set$key";

            if (method_exists($this, $method)) {
                call_user_func_array([$this, $method], $value);
            } else if ($strict) {
                throw new \InvalidArgumentException("Configuration value $key not recognized");
            }
        }
    }
}