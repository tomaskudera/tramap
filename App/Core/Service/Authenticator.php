<?php

namespace Core\Service;

use Nette;
use Nette\Security\Passwords;

class Authenticator implements Nette\Security\IAuthenticator
{
	const
		TABLE_LOGIN = 'user_login',
		COLUMN_LOGIN = 'login',
		COLUMN_PASSWORD_HASH = 'password',
		TABLE_IDENTITY = 'user_identity',
		COLUMN_LOGIN_ID = 'id';

	/** @var Nette\Database\Context */
	private $db;

	private $translator;


	public function __construct(Nette\Database\Context $database, Nette\Localization\ITranslator $translator)
	{
		$this->db = $database;
		$this->translator = $translator;
	}

	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($login, $password) = $credentials;

		$row = $this->db->table(self::TABLE_LOGIN)->where(self::COLUMN_LOGIN, $login)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException($this->translator->translate('errors.login-mismatch'), self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException($this->translator->translate('errors.login-mismatch'), self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update([
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			]);
		}
		$row = $this->db->table(self::TABLE_IDENTITY)->where(self::COLUMN_LOGIN_ID, $row->id)->fetch();
		$arr = $row->toArray();
		return new Nette\Security\Identity($row['id'], [$row['role']], $arr);
	}

	public function resetPassword($data)
	{
		$data['password'] = Passwords::hash($data['password']);
		bdump($data['password']);
		$this->db->table(self::TABLE_LOGIN)
			->where('login', $data['login'])
			->update(['password' => $data['password']]);
	}
}
