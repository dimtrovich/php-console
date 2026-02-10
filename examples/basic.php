<?php

use Ahc\Cli\Input\Command;

 require __DIR__ . '/../vendor/autoload.php';

class HelloCommand extends BlitzPHP\Console\Command
{
	protected string $group = 'Application';
	protected string $name = 'app:hello';
	protected string $description = 'Affiche un message de bienvenue';

	protected array $arguments = [
		'name' => ['Le nom de la personne à saluer']
	];

	protected function configure(Command $command)
	{
		parent::configure($command);
	}
	public function handle()
	{
		$this->info('Bonjour, ' . $this->argument('name') . ' ! Bienvenue dans BlitzPHP Console.');

		$choix = $this->menu('Choisissez une option', [
			'Option 1' => 'Description de l\'option 1',
			'Option 2' => 'Description de l\'option 2',
			'Option 3' => 'Description de l\'option 3',
		]);

		$this->info('Vous avez choisi : ' . $choix);

		exit;
		$confirm = $this->confirm('Voulez-vous continuer ?');
		$this->info('Vous avez répondu : ' . $confirm);



		$this->asciiArt('BlitzPHP', 'slant');
		$this->beep(3);

		$this->timeline([
			['2024-01-01', 'Lancement de BlitzPHP Console'],
			['2024-02-15', 'Ajout de la fonctionnalité X'],
			['2024-03-30', 'Correction de bugs et améliorations'],
		]);

		$this->withProgressBar([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], function($item) {
			sleep(1);
		});

		$this->heatmap([10, 20, 30, 40, 50, 60, 70, 80, 90, 100]);

		$this->animation(['-', '\\', '|', '/'], 5, 200000);
		$this->notify('Notification BlitzPHP', 'Ceci est une notification de test.');


	 }
}

$app = new BlitzPHP\Console\Application('My Console App', '1.0.0', 'fr');

$app->headtitle('BlitzPHP v0.13 - Interface en ligne de commande - Heure du serveur : ' . date('Y-m-d H:i:s'));
$app->showFooter(false);
$app->logo();

$app->addCommand(HelloCommand::class);
$app->command('foo:bar', 'foo bar baz');
$app->handle($_SERVER['argv']);
