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
 * UJM\ExoBundle\Entity\Interaction
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="UJM\ExoBundle\Entity\InteractionRepository")
 */
class Interaction
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
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var text $invite
     *
     * @ORM\Column(name="invite", type="text")
     */
    private $invite;

    /**
     * @var integer $ordre
     *
     * @ORM\Column(name="ordre", type="integer", nullable=true)
     */
    private $ordre;

    /**
     * @var text $feedBack
     *
     * @ORM\Column(name="feedBack", type="text", nullable=true)
     */
    private $feedBack;

    /**
     * @var boolean $locked_expertise
     *
     * @ORM\Column(name="locked_expertise", type="boolean", nullable=true)
     */
    private $locked_expertise;

    /**
     * @ORM\ManyToMany(targetEntity="UJM\ExoBundle\Entity\Document")
     */
    private $documents;

    /**
     * @ORM\ManyToOne(targetEntity="UJM\ExoBundle\Entity\Question", cascade={"remove"})
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity="UJM\ExoBundle\Entity\Hint", mappedBy="interaction", cascade={"remove"})
     */
    private $hints;
    

     /**
     * Constructs a new instance of Documents, hints
     */
    public function __construct()
    {
        $this->documents = new \Doctrine\Common\Collections\ArrayCollection;
        $this->hints = new \Doctrine\Common\Collections\ArrayCollection;
        $this->setLockedExpertise(FALSE);
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
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set invite
     *
     * @param string $invite
     */
    public function setInvite($invite)
    {
        $this->invite = $invite;
    }

    /**
     * Get invite
     *
     * @return text
     */
    public function getInvite()
    {
        return $this->invite;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;
    }

    /**
     * Get ordre
     *
     * @return integer 
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set feedBack
     *
     * @param text $feedBack
     */
    public function setFeedBack($feedBack)
    {
        $this->feedBack = $feedBack;
    }

    /**
     * Get feedBack
     *
     * @return text 
     */
    public function getFeedBack()
    {
        return $this->feedBack;
    }

    /**
     * Set locked_expertise
     *
     * @param boolean $lockedExpertise
     */
    public function setLockedExpertise($lockedExpertise)
    {
        $this->locked_expertise = $lockedExpertise;
    }

    /**
     * Get locked_expertise
     *
     * @return boolean 
     */
    public function getLockedExpertise()
    {
        return $this->locked_expertise;
    }
    
    /**
     * Gets an array of Documents.
     * 
     * @return array An array of Documents objects
     */
    public function getDocuments()
    {
        return $this->documents;
    }
    
    /**
     * Add $Document
     *
     * @param UJM\ExoBundle\Entity\Document $Document
     */
    public function addDocument(\UJM\ExoBundle\Entity\Document $document)
    {
        $this->document[] = $document;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function setQuestion(\UJM\ExoBundle\Entity\Question $question)
    {
        $this->question = $question;
    }

    public function getHints()
    {
        return $this->hints;
    }

    public function addHint(\UJM\ExoBundle\Entity\Hint $hint)
    {
        $this->hints[] = $hint;
        //le choix est bien lié à l'entité interactionqcm, mais dans l'entité choice il faut aussi lié l'interactionqcm
        //double travail avec les relations bidirectionnelles avec lesquelles il faut bien faire attention à garder les données cohérentes
        //dans un autre script il faudra exécuter $interaction->addHint() qui garde la cohérence entre les deux entités,
        //il ne faudra pas exécuter $hint->setInteraction(), car lui ne garde pas la cohérence
        $hint->setInteraction($this);
    }
    
    public function removeHint(\UJM\ExoBundle\Entity\Hint $hint)
    {
    }
    
    public function setHints ($hints)
    {
        foreach($hints as $hint)
        {
            $this->addHint($hint);
        }
    }
}