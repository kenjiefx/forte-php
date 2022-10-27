<?php

namespace Kenjiefx\Forte\Installer;

use Kenjiefx\Forte\GitHub\GitHubAPI;
use Kenjiefx\Forte\Services\PackageManager;
use Kenjiefx\Forte\Exceptions\NotFoundException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class Installer
{
    private PackageManager $Packagist;

    public function __construct(
        private OutputInterface $Output
        )
    {
        $this->Packagist = new PackageManager();
    }

    public function install()
    {

        $packages = $this->Packagist->getPackages();
        $local_composer = $this->Packagist->getComposer();

        $total_number_of_packages = count($packages);
        $total_number_of_steps = ($total_number_of_packages * 4)+2;

        $progressBar = new ProgressBar($this->Output,$total_number_of_steps);
        $progressBar->setFormat(
            "%status% \n%current%/%max% [%bar%] %percent:3s%%" 
        );
        $progressBar->setBarCharacter('<fg=green>⚬</>');
        $progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
        $progressBar->setProgressCharacter("<fg=green>➤</>");

        $progressBar->setMessage('Starting up...','status');
        $progressBar->advance();

        foreach ($packages as $name => $package) {

            $branch     = $package['branch']??'main';
            $repository = $package['repository'];

            $progressBar->setMessage('['.$repository.'] Connecting to repository...','status');
            $progressBar->advance();
            $api  = new GitHubAPI(CURLAUTH_BASIC);
            $refs = $api->getRefs($repository);
            $sha  = null;

            foreach ($refs as $ref) {
                if ($ref['ref']==='refs/heads/'.$branch) {
                    $sha = $ref['object']['sha'];
                }
            }

            if (null===$sha) {
                throw new NotFoundException('[github] branch not found: '.$branch);
            }

            $progressBar->setMessage('['.$repository.'] Loading repository tree...','status');
            $progressBar->advance();
            $trees = $api->getTree($repository,$sha);

            $withComposer = false;

            foreach ($trees['tree'] as $key => $tree) {

                if ($tree['path']==='composer.json') {

                    $progressBar->setMessage('['.$repository.'] Parsing repository composer.json...','status');
                    $progressBar->advance();

                    $withComposer      = true;
                    $blob              = $api->getFile($tree['url']);
                    $remote_composer   = json_decode(base64_decode($blob['content']),TRUE);
                    [$vendor,$library] = explode('/',$remote_composer['name']);

                    $vPath = ROOT.'/vendor/'.$vendor;
                    if (!is_dir($vPath)) mkdir($vPath);

                    $lPath = $vPath.'/'.$library;
                    if (!is_dir($lPath)) mkdir($lPath);

                    $repository_nmspc     = array_key_first($remote_composer['autoload']['psr-4']);
                    $repository_src       = $remote_composer['autoload']['psr-4'][$repository_nmspc];
                    $local_repository_loc = 'vendor/'.$vendor.'/'.$library.'/'.$repository_src;

                    $local_composer['autoload']['psr-4'][$repository_nmspc] = $local_repository_loc;

                    foreach ($remote_composer['require'] as $name => $version) {
                        $local_composer['require'][$name] = $version;
                    }

                }
            }

            if (!$withComposer) {
                throw new NotFoundException('[github] no composer.json found in the repository');
            }

            $progressBar->setMessage('['.$repository.'] Cloning repository...','status');
            $progressBar->advance();
            
            $this->download($repository,$trees['tree'],$lPath);

        }

        file_put_contents(ROOT.'/composer.json',json_encode($local_composer));

        $progressBar->setMessage('Installation finished!','status');
        $progressBar->advance();
        $progressBar->finish();

        echo PHP_EOL;

    }


    public function download(
        string $repository,
        array $tree,
        string $directory
        )
    {

        $api = new GitHubAPI(CURLAUTH_BASIC);

        foreach ($tree as $key => $item) {

            $path = $directory.'/'.$item['path'];

            if ($item['type']==='blob') {
                $blob    = $api->getFile($item['url']);
                $content = base64_decode($blob['content']);
                file_put_contents($path,$content);
            }
            elseif ($item['type']==='tree') {
                mkdir($path);
                $sha   = $item['sha'];
                $trees = $api->getTree($repository,$sha);
                $this->download($repository,$trees['tree'],$path);
            }
            else {

            }
        }
    }

}
