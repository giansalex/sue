<?php

/**
 * Class Security
 */
class Security
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var User
     */
    private $user;

    /**
     * Security constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
        $this->tryGetUser();
    }

    public function register($email, $password)
    {
        if ($this->repository->exist($email)) {
            return FALSE;
        }
        $user = new User();
        $user->setEmail($email)
            ->setPlainPassword($password)
            ->setEnable(true);

        $this->repository->add($user);
        $this->saveSession($user);
        $this->user = $user;

        return true;
    }

    public function login($email, $password)
    {
        $user = $this->repository->get($email, $password);
        if ($user === FALSE) {
            return false;
        }
        $this->saveSession($user);
        $this->user = $user;

        return true;
    }

    /**
     * @return bool
     */
    public function isLoggin()
    {
        return !empty($this->user);
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function logout()
    {
        unset($_SESSION['u_id']);
        unset($_SESSION['u_email']);
        session_destroy();
    }

    private function tryGetUser()
    {
        if (isset($_SESSION['u_id']) && isset($_SESSION['u_email'])) {
            $this->user = (new User())
                ->setId(intval($_SESSION['u_id']))
                ->setEmail($_SESSION['u_email']);
        }
    }

    private function saveSession(User $user)
    {
        $_SESSION['u_id'] = $user->getId();
        $_SESSION['u_email'] = $user->getEmail();
    }
}