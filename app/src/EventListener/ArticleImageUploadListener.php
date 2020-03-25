<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Entity\Article;
use App\Service\FileUploader;

class ArticleImageUploadListener
{
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Article) {
            return;
        }

        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Article) {
            return;
        }

        if ($args->getNewValue('image')) {
            if ($entity->getImage()->getPath() == $this->uploader->getTargetDir()) {
                $entity->setImage($entity->getImage()->getFilename());
            } else {
                $this->uploadFile($entity);
            }
        } else {
            $entity->setImage($args->getOldValue('image'));
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Article) {
            return;
        }

        if ($fileName = $entity->getImage()) {
            $entity->setImage(new File($this->uploader->getTargetDir() . '/' . $fileName));
        }
    }

    private function uploadFile($entity)
    {
        // загрузка работает только для сущностей Article
        if (!$entity instanceof Article) {
            return;
        }

        $file = $entity->getImage();

        // загружать только новые файлы
        if ($file instanceof UploadedFile) {
            $fileName = $this->uploader->upload($file);
            $entity->setImage($fileName);
        }
    }
}