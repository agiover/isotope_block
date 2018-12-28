<?php

namespace Drupal\isotope_block\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Class GalleryEntityForm.
 */
class GalleryEntityForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $gallery_entity = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $gallery_entity->label(),
      '#description' => $this->t("Label for the Gallery."),
      '#required' => TRUE,
    ];
    $vid = 'tipo_de_galeria';
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      $term_data[$term->tid] = $term->name;
    }
    $form['tid'] = [
      '#type' => 'select',
      '#title' => $this->t('Tipo'),
      '#options' => $term_data,
      '#default_value' => $gallery_entity->tid(),
    ];
    $form['imagen'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Imagen'),
      '#description' => $this->t('Imagen de fondo para el hero banner'),
      '#default_value' => [$gallery_entity->image()],
      '#weight' => '10',
      '#upload_validators' => ['file_validate_extensions' => ['jpg', 'png', 'jpeg'],],
      '#upload_location' => 'public://banner/',
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $gallery_entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\isotope_block\Entity\GalleryEntity::load',
      ],
      '#disabled' => !$gallery_entity->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $gallery_entity = $this->entity;
    // Save image as permanent.
    $image = $form_state->getValue('imagen');
    if ($image[0] != $this->entity->image()) {
      if (!empty($image[0])) {
        $file = File::load($image[0]);
        $file->setPermanent();
        $file->save();
        $file_usage = \Drupal::service('file.usage');
        $file_usage->add($file, 'isotope_block', 'gallery_entity', $gallery_entity->id());
      }
    }
    $gallery_entity->setImage($image[0]);

    $status = $gallery_entity->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Gallery.', [
          '%label' => $gallery_entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Gallery.', [
          '%label' => $gallery_entity->label(),
        ]));
    }
    $form_state->setRedirectUrl($gallery_entity->toUrl('collection'));
  }

}
