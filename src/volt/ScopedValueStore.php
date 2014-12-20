<?php
namespace volt;

class ScopedValueStore{
    private $valueStore;
    /** @var ScopedValueStore[] */
    private $children;
    public function __construct(){
        $this->children = [];
        $this->valueStore = [];
    }
    public function setValue(array $scope, $name, $value){
        if(count($scope) === 0){
            $this->valueStore[$name] = $value;
        }
        else {
            if (!isset($this->children[$scope[0]])) $this->children[$scope[0]] = new ScopedValueStore();
            $nextScope = array_shift($scope);
            $this->children[$nextScope]->setValue($scope, $name, $value);
        }
    }
    public function getValue(array $scope, $name, $value){
        if(count($scope) === 0){
            return (isset($this->valueStore[$name]) ? isset($this->valueStore[$name]) : null);
        }
        else {
            if (!isset($this->children[$scope[0]])) $this->children[$scope[0]] = new ScopedValueStore();
            $nextScope = array_shift($scope);
            return $this->children[$nextScope]->getValue($scope, $name, $value);
        }
    }
    public function getScopeValues(array $scope){
        if(count($scope) === 0){
            return $this->valueStore;
        }
        else {
            if (!isset($this->children[$scope[0]])) $this->children[$scope[0]] = new ScopedValueStore();
            $nextScope = array_shift($scope);
            return array_merge($this->valueStore, $this->children[$nextScope]->getScopeValues($scope));
        }
    }
}