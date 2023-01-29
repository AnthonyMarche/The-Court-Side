<?php

namespace App\Controller\Admin;

use App\Form\TeaserType;
use FFMpeg;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/teaser', name: 'app_teaser')]
class TeaserController extends AbstractController
{
    private const PATH_NEW_TEASER = 'uploads/teasersCreated/';
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    #[Route('/new', name: '_new')]
    public function teaserFormView(Request $request): Response
    {
        // Create form
        $teaserForm = $this->createForm(TeaserType::class);

        //clean directory teaser
        array_map('unlink', glob("uploads/teasersCreated/*.*"));

        $teaserForm->handleRequest($request);

        // If form is submitted call method to create teaser
        if ($teaserForm->isSubmitted() && $teaserForm->isValid()) {
            $file = $teaserForm->getData()['video'];
            $secondStart = $teaserForm->getData()['secondStart'];
            $duration = $teaserForm->getData()['duration'];

            $this->createTeaser($file, $secondStart, $duration);

            // Get teaser information
            $name = $this->getFileName($file) . $this->getFileExtension($file);
            $teaser = $this->getTeaserPath($file);

            // Verify is teaser was created, if not return flash message with error
            if (!file_exists($teaser)) {
                $this->addFlash('warning', 'Impossible de créer un teaser avec ces valeurs');
                return $this->render('admin/create_teaser.html.twig', [
                    'teaserForm' => $teaserForm->createView()
                ]);
            }

            // If file created, flash message then redirect to view teaser page
            $this->addFlash('success', 'Votre teaser a été créé, téléchargez le dès maintenant !');

            return $this->viewNewTeaser($teaser, $name);
        }

        return $this->render('admin/create_teaser.html.twig', [
            'teaserForm' => $teaserForm->createView()
        ]);
    }

    // View created teaser
    public function viewNewTeaser(string $teaser, string $name): Response
    {
        return $this->render('admin/download_teaser.html.twig', [
            'teaser' => $teaser,
            'name' => $name,
        ]);
    }

    // Download created teaser
    #[Route('/download/{name}', name: '_download')]
    public function downloadTeaser(string $name): BinaryFileResponse
    {
        $teaser = self::PATH_NEW_TEASER . $name;

        return $this->file($teaser, $name);
    }

    // Create teaser with file and time define
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

        //Get video duration then verify if it's possible to create teaser with this timer
        $baseDuration = $video->getStreams()->first()->get('duration');

        if ($secondStart + $duration > $baseDuration) {
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
        $extension = $this->getFileExtension($file);
        $teaser = $this->getTeaserPath($file);

        // Save teaser
        if ($extension === '.mp4') {
            $video->save(new FFMpeg\Format\Video\X264(), $teaser);
        } elseif ($extension === '.ogg') {
            $video->save(new FFMpeg\Format\Video\Ogg(), $teaser);
        } elseif ($extension === '.webm') {
            $video->save(new FFMpeg\Format\Video\WebM(), $teaser);
        } elseif ($extension === '.wmv') {
            $video->save(new FFMpeg\Format\Video\WMV(), $teaser);
        }
    }

    // Get file name and create teaser name
    public function getFileName(File $file): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        return 'teaser-' . $this->slugger->slug($originalName)->lower();
    }

    // Get file extension
    public function getFileExtension(File $file): string
    {
        return '.' . $file->guessExtension();
    }

    // Get path + name + extension of teaser
    public function getTeaserPath(File $file): string
    {
        $name = $this->getFileName($file);
        $extension = $this->getFileExtension($file);

        return self::PATH_NEW_TEASER . $name . $extension;
    }
}
