<?php

/**
 * ExoOnLine
 * Copyright or © or Copr. Université Jean Monnet (France), 2012
 * dsi.dev@univ-st-etienne.fr
 *
 * This software is a computer program whose purpose is to [describe
 * functionalities and technical features of your software].
 *
 * This software is governed by the CeCILL license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and  rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty  and the software's author,  the holder of the
 * economic rights,  and the successive licensors  have only  limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risks associated
 * with loading,  using,  modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean  that it is complicated to manipulate,  and  that  also
 * therefore means  that it is reserved for developers  and  experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensured and,  more generally, to use and operate it in the
 * same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL license and that you accept its terms.
*/

namespace UJM\ExoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * UJM\ExoBundle\Entity\User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="UJM\ExoBundle\Entity\UserRepository")
 * @DoctrineAssert\UniqueEntity("username")
 */
class User implements UserInterface, EquatableInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $firstname
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string $mail
     *
     * @ORM\Column(name="mail", type="string", length=255, nullable=true)
     */
    private $mail;

    /**
     * @ORM\ManyToMany(targetEntity="UJM\ExoBundle\Entity\Role")
     */
    private $userRole;

    /**
     * @ORM\ManyToMany(targetEntity="UJM\ExoBundle\Entity\Groupes")
     */
    private $groupes;

    
    /**
     * Constructs a new instance of UserRole / groupes
     */
    public function __construct()
    {
        $this->userRole = new ArrayCollection();
        $this->groupes = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = md5($password);
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set mail
     *
     * @param string $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    public function getUserRole()
    {
        return $this->userRole;
    }

    /**
     * Gets the user salt.
     *
     * @return string The salt.
     */
    public function getSalt()
    {

    }

    /**
     * Erases the user credentials.
     */
    public function eraseCredentials()
    {

    }

    /**
     * Gets an array of roles.
     *
     * @return array An array of Role objects
     */
    public function getRoles()
    {
        return $this->getUserRole()->toArray();
    }

    /**
     * Add userRole
     *
     * @param UJM\ExoBundle\Entity\Role $userRole
     */
    public function addRole(\UJM\ExoBundle\Entity\Role $userRole)
    {
        $this->userRole[] = $userRole;
    }

    /**
     * Add groupe
     *
     * @param UJM\ExoBundle\Entity\Groupes $groupe
     */
    public function addGroupe(\UJM\ExoBundle\Entity\Groupes $groupe)
    {
        $this->Groupes[] = $groupe;
    }

    /**
     * Gets an array of groupes.
     *
     * @return array An array of Groupes objects
     */
    public function getGroupes()
    {
        return $this->groupes;
    }
    
    /**
    * Compares this user to another to determine if they are the same.
    *
    * @param UserInterface $user The user
    * @return boolean True if equal, false othwerwise.
    */
    public function isEqualTo(UserInterface $user)
    {
        return md5($this->getUsername()) == md5($user->getUsername());
    }



    public function __toString()
    {
        return $this->name;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list ($this->id, $this->username) = unserialize($serialized);
    }
}