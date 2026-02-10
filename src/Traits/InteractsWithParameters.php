<?php

namespace BlitzPHP\Console\Traits;

trait InteractsWithParameters
{
    /**
     * Parametres recus apres executions
	 *
	 * @var array{
	 *      arguments: array<string, mixed>,
	 *      options: array<string, mixed>
	 * }
     */
    private array $parameters = [];

	/**
	 * Defini les parametres recus apres execution de la commande
	 *
	 * @internal
	 */
	public function setParameters(array $arguments, array $options): void
	{
		$this->parameters = [
			'arguments' => $arguments,
			'options' => $options
		];
	}

	/**
	 * Récupère la valeur d'un argument de la commande
	 *
	 * @param string $name Le nom de l'argument
	 * @param mixed|null $default La valeur par défaut à retourner si l'argument n'est pas défini
	 * @return mixed La valeur de l'argument ou la valeur par défaut
	 */

	public function argument(string $name, mixed $default = null): mixed
	{
		return $this->parameters['arguments'][$name] ?? $default;
	}

	public function option(string $name, mixed $default = null): mixed
	{
		return $this->parameters['options'][$name] ?? $default;
	}

	public function options(): array
	{
		 return $this->parameters['options'];
	}

	public function arguments(): array
	{
		 return $this->parameters['arguments'];
	}

	/**
     * Check if argument exists
     */
    public function hasArgument(string $name): bool
    {
        return isset($this->parameters['arguments'][$name]);
    }

    /**
     * Check if option exists
     */
    public function hasOption(string $name): bool
    {
        return isset($this->parameters['options'][$name]);
    }

	/**
	 * Récupère la valeur d'un argument ou d'une option de la commande
	 *
	 * @param string $key Le nom de l'argument ou de l'option
	 * @return mixed La valeur de l'argument ou de l'option, ou null si elle n'existe pas
	 */
	public function parameter(string $key, mixed $default = null): mixed
	{
		$params = array_merge($this->parameters['arguments'], $this->parameters['options']);

		return $params[$key] ?? $default;
	}
}
