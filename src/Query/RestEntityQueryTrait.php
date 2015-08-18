<?php
/**
 * @file
 * A trait list of operators.
 */

namespace Drupal\morphd\Query;

trait RestEntityQueryTrait {

  public function opEquals($field = '', $value = '') {
    $this->query->condition($field, $value);
    return $this;
  }

  public function opNotEquals($field = '', $value = '') {
    $this->query->condition($field, $value, '!=');
    return $this;
  }

  public function opIn($field = '', $values = []) {
    $this->query->condition($field, $values, 'in');
    return $this;
  }

  public function opNotIn($field = '', $values = []) {
    $this->query->condition($field, $values, 'not in');
    return $this;
  }

  public function opGreaterThan($field = '', $value = '') {
    $this->query->condition($field, (int) $value, '>');
    return $this;
  }

  public function opGreaterThanEqual($field = '', $value = '') {
    $this->query->condition($field, (int) $value, '>=');
    return $this;
  }

  public function opLessThan($field = '', $value = '') {
    $this->query->condition($field, (int) $value, '<');
    return $this;
  }

  public function opLessThanEqual($field = '', $value = '') {
    $this->query->condition($field, (int) $value, '<=');
    return $this;
  }

}
