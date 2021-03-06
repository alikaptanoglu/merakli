<?php
/**
 * Class to manage users in the application
 *
 * Basic CRUD and session methods
 *
 * @author     Midori Kocak <mtkocak@mtkocak.net>
 */
namespace Midori\Cms;

/**
 * Class Users
 *
 * @package Midori\Cms
 */

/**
 * Class Users
 *
 * @package Midori\Cms
 */
class Users extends Assets
{

    /**
     * Method that adds a user
     *
     * TODO Check username if exists.
     *
     * @param null $username
     * @param null $email
     * @param null $password
     * @param null $password2
     * @return array|bool
     */
    public function add($username = null, $email = null, $password = null, $password2 = null)
    {
        if (!$this->checkLogin()) {
            return false;
        }

        if ($password != $password2) {
            return false;
        }

        // All of three variables has not to be null.
        if ($username != null && $email != null && $password != null) {

            $exists = $this->db->select('users')
                ->where('username', $username)
                ->run();

            if ($exists) {
                return array(
                    'render' => true,
                    'template' => 'admin',
                    'message' => 'User already exists!'
                );
            }

            $insert = $this->db->insert('users')->set(array(
                "username" => $username,
                "password" => md5($password),
                "email" => $email
            ));

            if ($insert) {

                return true;
            } else {
                return false;
            }
        } else {
            return array(
                'render' => true,
                'template' => 'admin'
            );
        }
    }

    /**
     * Show one user in admin context
     *
     * @param int $id
     * @return array|bool
     */
    public function view($id)
    {

        // Checks if user is logged in or there is user
        if (!$this->checkLogin()) {
            return false;
        }

        $query = $this->db->select('users')
            ->where('id', $id)
            ->run();

        if ($query) {
            $user = $query[0];


            $result = array(
                'template' => 'admin',
                'user' => $user,
                'render' => true
            );
            return $result;
        }

        return null;
    }

    /**
     * Show all users in admin context
     *
     * @return array|bool
     */
    public function show()
    {
        if (!$this->checkLogin()) {
            return false;
        }

        $query = $this->db->select('users')->run();

        if ($query) {
            $result = array(
                'render' => true,
                'template' => 'admin',
                'users' => $query
            );
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Show all users in public context
     *
     * @return array|bool
     */
    public function index()
    {
        return $this->show();
    }

    /**
     * Edit user selecting by it's id
     *
     * @param null $id
     * @param null $username
     * @param null $password
     * @param null $password2
     * @param null $email
     * @return array|bool
     */
    public function edit($id = null, $username = null, $password = null, $password2 = null, $email = null)
    {
        if (!$this->checkLogin()) {
            return false;
        }

        if ($password != $password2) {
            return false;
        }

        if ($id != null && $username != null && $password != null && $email != null) {

            $update = $this->db->update('user')
                ->where('id', $id)
                ->set(array(
                    "username" => $username,
                    "password" => md5($password),
                    "email" => $email
                ));

            if ($update) {
                return true;
            } else {
                return false;
            }
        } else {
            $oldData = $this->view($id);
            return array(
                'template' => 'admin',
                'render' => true,
                'user' => $oldData['user']
            );
        }
    }

    /**
     * Login method that creates sessions if username and password is true
     *
     * @param null $username
     * @param null $password
     * @return array|bool
     */
    public function login($username = null, $password = null)
    {
        if (!$this->checkLogin()) {
            // User is not logged in.

            $query = $this->db->select('users')
                ->where('username', $username)
                ->where('password', md5($password))
                ->run();

            if ($query) {
                $user = $query[0];
                // If username or password is not correct
                if (!$user) {
                    return array(
                        'template' => 'public',
                        'render' => true,
                        'message' => 'Username or password is not correct!'
                    );
                }

                $this->id = $user['id'];
                $this->$username = $user['username'];
                $this->$password = $user['password'];

                // Create sessions

                $_SESSION['username'] = $user['username'];
                $_SESSION['id'] = $user['id'];

                return array(
                    'template' => 'admin',
                    'render' => false,
                    'message' => 'Logged in!',
                    'user' => $user
                );
            }
        } else {
            header('Location:' . LINK_PREFIX . '/posts/show');
            return array(
                'template' => 'admin',
                'render' => false,
                'message' => 'Already logged in!'
            );
        }
        return false;
    }

    /**
     * Logout method.
     * Destroys session data.
     *
     * @return array
     */
    public function logout()
    {
        session_destroy();
        header('Location:' . LINK_PREFIX . '/');
        return array(
            'template' => 'public',
            'render' => false,
            'message' => 'You are logged out.'
        );
    }

    /**
     * Delete user by it's id.
     *
     * @param int $id
     * @return array|bool
     */
    public function delete($id)
    {
        if (!$this->checkLogin()) {
            return false;
        }

        $query = $this->db->delete('users')
            ->where('id', $id)
            ->done();

        return array(
            'template' => 'admin',
            'render' => false
        );
    }
}

?>

