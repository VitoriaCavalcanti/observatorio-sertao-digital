<?php
namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;

final class PostType extends AbstractType
{
    public function __construct(private readonly SluggerInterface $slugger) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('titulo')->add('resumo', TextareaType::class)->add('conteudo', TextareaType::class, ['attr' => ['rows' => 14]])
            ->add('status', ChoiceType::class, ['choices' => ['Rascunho' => Post::STATUS_RASCUNHO, 'Publicado' => Post::STATUS_PUBLICADO, 'Arquivado' => Post::STATUS_ARQUIVADO]])
            ->add('publico', CheckboxType::class, ['required' => false])->add('fixado', CheckboxType::class, ['required' => false])
            ->add('prioridade', IntegerType::class)->add('publicadoEm', DateTimeType::class, ['required' => false, 'widget' => 'single_text'])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void { $post = $event->getData(); if ($post instanceof Post) { $post->setSlug($this->slugger->slug($post->getTitulo())->lower()->toString()); $post->touch(); if ($post->getStatus() === Post::STATUS_PUBLICADO && !$post->getPublicadoEm()) $post->setPublicadoEm(new \DateTimeImmutable()); } });
    }
    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class' => Post::class]); }
}
