<?php

namespace Drupal\isotope_block\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\file\Entity\File;

/**
 * Defines the Gallery entity.
 *
 * @ConfigEntityType(
 *   id = "gallery_entity",
 *   label = @Translation("Gallery"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\isotope_block\GalleryEntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\isotope_block\Form\GalleryEntityForm",
 *       "edit" = "Drupal\isotope_block\Form\GalleryEntityForm",
 *       "delete" = "Drupal\isotope_block\Form\GalleryEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\isotope_block\GalleryEntityHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "gallery_entity",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/gallery_entity/{gallery_entity}",
 *     "add-form" = "/admin/structure/gallery_entity/add",
 *     "edit-form" = "/admin/structure/gallery_entity/{gallery_entity}/edit",
 *     "delete-form" = "/admin/structure/gallery_entity/{gallery_entity}/delete",
 *     "collection" = "/admin/structure/gallery_entity"
 *   }
 * )
 */
class GalleryEntity extends ConfigEntityBase implements GalleryEntityInterface {

  /**
   * The Gallery ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Gallery label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Taxonomy ID.
   *
   * @var string
   */
  protected $tid;

  /**
   * Image entity.
   *
   * @var string
   */
  protected $image;

  /**
   * The weight of this role in administrative listings.
   *
   * @var int
   */
  protected $weight;

  /**
   * {@inheritdoc}
   */
  public function tid() {
    return $this->get('tid');
  }

  /**
   * {@inheritdoc}
   */
  public function image() {
    return $this->get('image');
  }

  /**
   * {@inheritdoc}
   */
  public function imageURI() {
    $image = $this->image();
    if (!empty($image)) {
      if ($file = File::load($image)) {
        $uri = $file->getFileUri();
      }
    }
    return $uri;
  }

  /**
   * {@inheritdoc}
   */
  public function imageURL() {
    $uri = $this->imageURI();
    $url = file_url_transform_relative(file_create_url($uri));
    return $url;
  }

  /**
   * {@inheritdoc}
   */
  public function imageStyle($image_style) {
    $uri = $this->imageURI();
    $style = \Drupal::entityTypeManager()->getStorage('image_style')->load($image_style);
    $url = $style->buildUrl($uri);
    return $url;
  }

  /**
   * {@inheritdoc}
   */
  public function setImage($image) {
    $this->set('image', $image);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->get('weight');
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight($weight) {
    $this->set('weight', $weight);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function postLoad(EntityStorageInterface $storage, array &$entities) {
    parent::postLoad($storage, $entities);
    // Sort the queried payment by their weight.
    // See \Drupal\Core\Config\Entity\ConfigEntityBase::sort().
    uasort($entities, 'static::sort');
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    if (!isset($this->weight) && ($entities = $storage->loadMultiple())) {
      // Set a role weight to make this new role last.
      $max = array_reduce($entities, function ($max, $entity) {
        return $max > $entity->weight ? $max : $entity->weight;
      });
      $this->weight = $max + 1;
    }
  }
}
