<?php

class ScopedValueStore{
    private $valueStore;
    /** @var ScopedValueStore[] */
    private $children;
    public function __construct(){
        $this->children = [];
        $this->valueStore = [];
    }
    public function setValue(array $scope, $value){
        if(count($scope) === 1){
            $this->valueStore[$scope[0]] = $value;
        }
        else {
            if (!isset($this->children[$scope[0]])) $this->children[$scope[0]] = new ScopedValueStore();
            $nextScope = array_shift($scope);
            $this->children[$nextScope]->setValue($scope, $value);
        }
    }
    public function getValue(array $scope, $value){
        if(count($scope) === 1){
            return (isset($this->valueStore[$scope[0]]) ? isset($this->valueStore[$scope[0]]) : null);
        }
        else {
            if (!isset($this->children[$scope[0]])) $this->children[$scope[0]] = new ScopedValueStore();
            $nextScope = array_shift($scope);
            return $this->children[$nextScope]->getValue($scope, $value);
        }
    }
    public function getScopeValues(array $scope, $value){
        if(count($scope) === 1){
            return $this->valueStore;
        }
        else {
            if (!isset($this->children[$scope[0]])) $this->children[$scope[0]] = new ScopedValueStore();
            $nextScope = array_shift($scope);
            return array_merge($this->valueStore, $this->children[$nextScope]->getScopeValues($scope, $value));
        }
    }
}