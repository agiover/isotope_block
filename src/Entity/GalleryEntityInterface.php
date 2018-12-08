<?php

namespace Drupal\isotope_block\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Gallery entities.
 */
interface GalleryEntityInterface extends ConfigEntityInterface {

  /**
   * Returns term id.
   *
   * @return string
   *   The term id.
   */
  public function tid();  
  
  /**
   * Returns image.
   *
   * @return string
   *   The image entity.
   */
  public function image();  
  
  /**
   * Returns image URI.
   *
   * @return string
   *   The image URI.
   */
  public function imageURI();  
  
  /**
   * Returns image URL.
   *
   * @return string
   *   The image URL.
   */
  public function imageURL();  
  
  /**
   * Returns image URL.
   *
   * @param string $image_style
   *   The desired image style.
   * 
   * @return string
   *    The styled image url
   */
  public function imageStyle($image_style);  

  /**
   * Sets the image to the given value.
   *
   * @param int $image
   *   The desired image.
   *
   * @return $this
   */
  public function setImage($image);
  
  /**
   * Returns the weight.
   *
   * @return int
   *   The weight of this role.
   */
  public function getWeight();

  /**
   * Sets the weight to the given value.
   *
   * @param int $weight
   *   The desired weight.
   *
   * @return $this
   */
  public function setWeight($weight);

}
