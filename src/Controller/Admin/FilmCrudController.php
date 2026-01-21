<?php

namespace App\Controller\Admin;

use App\Entity\Film;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FilmCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Film::class;
    }

    public function configureFields(string $pageName): iterable
    {
        //liste des attributs à afficher dans LES écrans d'admin
        yield TextField::new('title', 'Titre');
        yield IntegerField::new('releaseYear', 'Année de sortie');
        yield TextEditorField::new('synopsis', 'Synopsis');


        // Affichage dans l'index (liste des films)
        yield AssociationField::new('platform', 'Plateformes')
            ->onlyOnIndex() 
            ->formatValue(function ($value, $entity) {
            // $value = Collection d'objets Platforme
            $names = $entity->getPlatform()->map(fn($p) => $p->getName())->toArray();
            return implode(', ', $names);
            });

        // Affichage dans create / edit
        yield AssociationField::new('platform', 'Plateformes')
            ->onlyOnForms()
            ->setFormTypeOptions([
                'by_reference' => false,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name', 
            ]);
    }
}
