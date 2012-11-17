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

/**
 * UJM\ExoBundle\Entity\InteractionOpen
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="UJM\ExoBundle\Entity\InteractionOpenRepository")
 */
class InteractionOpen
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
     * @var boolean $orthography_correct
     *
     * @ORM\Column(name="orthography_correct", type="boolean")
     */
    private $orthography_correct;

    /**
     * @ORM\OneToOne(targetEntity="UJM\ExoBundle\Entity\Interaction")
     */
     private $interaction;

     /**
     * @ORM\ManyToMany(targetEntity="UJM\ExoBundle\Entity\Unit")
     */
     private $unit;

     /**
     * @ORM\ManyToOne(targetEntity="UJM\ExoBundle\Entity\TypeOpenQuestion")
     */
     private $typeopenquestion;

     public function __construct()
     {
        $this->unit = new \Doctrine\Common\Collections\ArrayCollection;
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
     * Set orthography_correct
     *
     * @param boolean $orthographyCorrect
     */
    public function setOrthographyCorrect($orthographyCorrect)
    {
        $this->orthography_correct = $orthographyCorrect;
    }

    /**
     * Get orthography_correct
     *
     * @return boolean 
     */
    public function getOrthographyCorrect()
    {
        return $this->orthography_correct;
    }

    public function getInteraction()
    {
        return $this->interaction;
    }

    public function setInteraction(UJM\ExoBundle\Entity\Interaction $interaction)
    {
        $this->interaction = $interaction;
    }

    /**
     * Gets an array of Units.
     *
     * @return array An array of Units objects
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Add $Unit
     *
     * @param UJM\ExoBundle\Entity\Unit $Unit
     */
    public function addUnit(UJM\ExoBundle\Entity\Unit $unit)
    {
        $this->unit[] = $unit;
    }

    public function getTypeOpenQuestion()
    {
        return $this->typeopenquestion;
    }

    public function setTypeOpenQuestion(UJM\ExoBundle\Entity\TypeOpenQuestion $typeOpenQuestion)
    {
        $this->typeopenquestion = $typeOpenQuestion;
    }
}