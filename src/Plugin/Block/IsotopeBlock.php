<?php

namespace Drupal\isotope_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'IsotopeBlock' block.
 *
 * @Block(
 *  id = "isotope_block",
 *  admin_label = @Translation("Isotope block"),
 * )
 */
class IsotopeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $args = self::get_arguments('tipo_de_galeria');
    $build['isotope_block'] = [
      '#theme' => 'isotope_block',
      '#taxonomies' => $args['taxonomies'],
      '#galleries' => $args['galleries'],
    ];
    $build['isotope_block']['#cache']['max-age'] = 0;

    return $build;
  }

  public function get_arguments($vid) {
    $galleries = [];
    $taxonomies = [];
    $image_style = "blueimp";
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      $term_data[$term->tid] = $term->name;
    }
    $entities = \Drupal::entityTypeManager()
      ->getStorage('gallery_entity')
      ->loadMultiple();
    foreach ($entities as $entity) {
      $galleries[] = [
        'title' => $entity->label(),
        'thumbnail' => $entity->imageStyle($image_style),
        'image' => $entity->imageURL(),
        'class' => 'taxonomy-' . $entity->tid(),
      ];
      $taxonomies[$entity->tid()] = [
        'title' => $term_data[$entity->tid()],
        'class' => 'taxonomy-' . $entity->tid(),
      ];
    }
    return [
      'galleries' => $galleries,
      'taxonomies' => $taxonomies,
    ];
  }
}
