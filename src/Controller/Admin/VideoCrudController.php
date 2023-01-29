<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use App\Form\TeaserType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Translation\TranslatableMessage;
use FFMpeg;

#[IsGranted('ROLE_ADMIN')]
class VideoCrudController extends AbstractCrudController
{
    private const PATH_VIDEO = 'uploads/videos';
    private const PATH_TEASER = 'uploads/teasers';
    private const PATH_NEW_TEASER = 'uploads/teasersCreated/';

    private SluggerInterface $slugger;
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(SluggerInterface $slugger, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->slugger = $slugger;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Video::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('crud/new', 'admin/new.html.twig')
            ->setEntityLabelInSingular(new TranslatableMessage('entity.video', ['parameter' => 'value'], 'admin'))
            ->setEntityLabelInPlural(new TranslatableMessage('entity.videos', ['parameter' => 'value'], 'admin'))
            ->setPageTitle(
                'index',
                new TranslatableMessage(
                    'entity.listOfVideos',
                    ['parameter' => 'value'],
                    'admin'
                )
            )
            ->setSearchFields(['title', 'description', 'category.name'])
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new(
                'title',
                new TranslatableMessage(
                    'entity.title',
                    ['parameter' => 'value'],
                    'admin'
                )
            ),

            TextareaField::new(
                'description',
                new TranslatableMessage(
                    'entity.description',
                    ['parameter' => 'value'],
                    'admin'
                )
            )
                ->setMaxLength(115),

            BooleanField::new(
                'isPrivate',
                new TranslatableMessage(
                    'entity.visibility',
                    ['parameter' => 'value'],
                    'admin'
                )
            ),

            AssociationField::new(
                'category',
                new TranslatableMessage(
                    'entity.category',
                    ['parameter' => 'value'],
                    'admin'
                )
            ),

            AssociationField::new(
                'tag',
                new TranslatableMessage(
                    'entity.tag',
                    ['parameter' => 'value'],
                    'admin'
                )
            )
                ->onlyOnForms(),

            ImageField::new('url', new TranslatableMessage('entity.file', ['parameter' => 'value'], 'admin'))
                ->onlyWhenCreating()
                ->setBasePath(self::PATH_VIDEO)
                ->setUploadDir('public/uploads/videos')
                ->setUploadedFileNamePattern(self::PATH_VIDEO . '/[slug]-[timestamp].[extension]')
                ->setHelp(new TranslatableMessage('file.extensions', ['parameter' => 'value'], 'admin')),

            ImageField::new('teaser', new TranslatableMessage('entity.teaser', ['parameter' => 'value'], 'admin'))
                ->onlyOnForms()
                ->setBasePath(self::PATH_TEASER)
                ->setUploadDir('public/uploads/teasers')
                ->setUploadedFileNamePattern(self::PATH_TEASER . '/teaser-[slug]-[timestamp].[extension]')
                ->setHelp(new TranslatableMessage('file.extensions', ['parameter' => 'value'], 'admin')),

            DateTimeField::new(
                'createdAt',
                new TranslatableMessage(
                    'entity.createdAt',
                    ['parameter' => 'value'],
                    'admin'
                )
            )
                ->onlyOnIndex()
                ->setFormat('dd/MM/YYYY'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Video) {
            return;
        }

        /** @var \App\Entity\User */
        $user = $this->getUser();
        $entityInstance->setUser($user);

        $slug = $this->slugger->slug($entityInstance->getTitle());
        $entityInstance->setSlug($slug);

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function createEntity(string $entityFqcn): Video
    {
        $video = new Video();

        $video->setNumberOfView(0);
        $video->setNumberOfLike(0);
        $video->setCreatedAt(new DateTime());

        return $video;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

        if (!$entityInstance instanceof Video) {
            return;
        }

        /** @var \App\Entity\User */
        $user = $this->getUser();
        $entityInstance->setUser($user);

        $slug = $this->slugger->slug($entityInstance->getTitle());
        $entityInstance->setSlug($slug);

        $entityInstance->setUpdatedAt(new DateTime());

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $video = $entityInstance->getUrl();
        $teaser = $entityInstance->getTeaser();

        unlink($video);
        if ($teaser) {
            unlink($teaser);
        }

        $entityManager->remove($entityInstance);
        $entityManager->flush();
    }

    #[Route('/teaser', name: 'app_teaser')]
    public function teaserFormView(Request $request): Response
    {
        // Create form
        $teaserForm = $this->createForm(TeaserType::class);

        //clean directory teaser
        array_map('unlink', glob("uploads/teasersCreated/*.*"));

        $teaserForm->handleRequest($request);

        // If form is submitted call method to create a teaser then return
        if ($teaserForm->isSubmitted() && $teaserForm->isValid()) {
            $file = $teaserForm->getData()['video'];
            $secondStart = $teaserForm->getData()['secondStart'];
            $duration = $teaserForm->getData()['duration'];

            $this->createTeaser($file, $secondStart, $duration);

            // Get teaser information
            $name = $this->getFileName($file);
            $extension = $this->getFileExtension($file);

            $teaser = self::PATH_NEW_TEASER . $name . $extension;

            if (!file_exists($teaser)) {
                $this->addFlash('warning', 'Impossible de creer un teaser avec ces valeurs');
                return $this->render('admin/create_teaser.html.twig', [
                    'teaserForm' => $teaserForm->createView()
                ]);
            }
            $this->addFlash('success', 'Votre teaser a été téléchargé');

            return $this->file($teaser, $name)->deleteFileAfterSend();
        }

        return $this->render('admin/create_teaser.html.twig', [
            'teaserForm' => $teaserForm->createView()
        ]);
    }

    public function createTeaser(File $file, int $secondStart, int $duration): void
    {
        // Get FFMpeg binary file to create object
        $ffmpeg = FFMpeg\FFMpeg::create([
            'ffmpeg.binaries' => $this->getParameter('ffmpeg_file_path'),
            'ffprobe.binaries' => $this->getParameter('ffprobe_file_path'),
            'timeout' => 3600,
            'ffmpeg.threads' => 12,
        ]);

        // Open file with FFMpeg
        $video = $ffmpeg->open($file);

        //Get video duration
        $baseDuration = $video->getStreams()->first()->get('duration');

        if ($secondStart < 0 || $duration < 1 || $secondStart + $duration > $baseDuration) {
            return;
        }

        // Create teaser with seconds define
        $video
            ->filters()
            ->clip(
                FFMpeg\Coordinate\TimeCode::fromSeconds($secondStart),
                FFMpeg\Coordinate\TimeCode::fromSeconds($duration)
            );

        // Get teaser information
        $name = $this->getFileName($file);
        $extension = $this->getFileExtension($file);

        $teaser = self::PATH_NEW_TEASER . $name . $extension;

        // Save teaser
        $video->save(new FFMpeg\Format\Video\X264(), $teaser);
    }

    public function getFileName(File $file): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        return 'teaser-' . $this->slugger->slug($originalName)->lower();
    }

    public function getFileExtension(File $file): string
    {
        return '.' . $file->guessExtension();
    }
}
