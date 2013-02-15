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
 * UJM\ExoBundle\Entity\InteractionHole
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="UJM\ExoBundle\Entity\InteractionHoleRepository")
 */
class InteractionHole
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
     * @var text $html
     *
     * @ORM\Column(name="html", type="text")
     */
    private $html;
    
    /**
     * @var text $htmlWhithoutValue
     *
     * @ORM\Column(name="htmlWhithoutValue", type="text")
     */
    private $htmlWhithoutValue;

    /**
     * @ORM\OneToOne(targetEntity="UJM\ExoBundle\Entity\Interaction", cascade={"remove"})
     */
    private $interaction;
    
    /**
     * @ORM\OneToMany(targetEntity="UJM\ExoBundle\Entity\Hole", mappedBy="interactionHole", cascade={"remove"})
     */
    private $holes;
    
    /**
     * Constructs a new instance of choices
     */
    public function __construct()
    {
        $this->holes = new \Doctrine\Common\Collections\ArrayCollection;
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
     * Set html
     *
     * @param text $html
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }

    /**
     * Get html
     *
     * @return text 
     */
    public function getHtml()
    {
        return $this->html;
    }
    
    /**
     * Set htmlWhithoutValue
     *
     * @param text $htmlWhithoutValue
     */
    public function setHtmlWhithoutValue($html)
    {
        $this->htmlWhithoutValue = $html;
    }

    /**
     * Get htmlWhithoutValue
     *
     * @return text 
     */
    public function getHtmlWhithoutValue()
    {
        return $this->htmlWhithoutValue;
    }
    
    public function getInteraction()
    {
        return $this->interaction;
    }

    public function setInteraction(\UJM\ExoBundle\Entity\Interaction $interaction)
    {
        $this->interaction = $interaction;
    }
    
    public function getHoles()
    {
        return $this->holes;
    }

    public function addHole(\UJM\ExoBundle\Entity\Hole $hole)
    {
        $this->holes[] = $hole;
        //le trou est bien lié à l'entité interactionHole, mais dans l'entité hole il faut aussi lié l'interactionhole
        //double travail avec les relations bidirectionnelles avec lesquelles il faut bien faire attention à garder les données cohérentes
        //dans un autre script il faudra exécuter $interactionHole->addHole() qui garde la cohérence entre les deux entités,
        //il ne faudra pas exécuter $hole->setInteractionHole(), car lui ne garde pas la cohérence
        $hole->setInteractionHole($this);
    }

    public function removeHole(\UJM\ExoBundle\Entity\Hole $Hole)
    {
        
    }
    
}