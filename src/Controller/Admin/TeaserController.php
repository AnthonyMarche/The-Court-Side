<?php

namespace App\Controller\Admin;

use App\Form\TeaserType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use FFMpeg;

class TeaserController extends AbstractController
{
    private const PATH_NEW_TEASER = 'uploads/teasersCreated/';

    private SluggerInterface $slugger;
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(SluggerInterface $slugger, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->slugger = $slugger;
        $this->adminUrlGenerator = $adminUrlGenerator;
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
