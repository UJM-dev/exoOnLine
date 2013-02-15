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

namespace UJM\ExoBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

use UJM\ExoBundle\Entity\InteractionHole;
use UJM\ExoBundle\Entity\User;
use UJM\ExoBundle\Entity\ExerciseQuestion;
use UJM\ExoBundle\Entity\Exercise;


class InteractionHoleHandler
{
    protected $form;
    protected $request;
    protected $em;
    protected $user;
    protected $exercise;
    
    public function __construct(Form $form, Request $request, EntityManager $em, User $user, $exercise=-1)
    {
        $this->form     = $form;
        $this->request  = $request;
        $this->em       = $em;
        $this->user     = $user;
        $this->exercise = $exercise;
    }

    public function processAdd()
    {
        if( $this->request->getMethod() == 'POST' )
        {
            $this->form->bindRequest($this->request);

            if( $this->form->isValid() )
            {
                $this->onSuccessAdd($this->form->getData());
                return true;
            }
        }

        return false;
    }

    private function onSuccessAdd(InteractionHole $interHole)
    {
        // \ pour instancier un objet du namespace global et non pas de l'actuel
        $interHole->getInteraction()->getQuestion()->setDateCreate(new \Datetime());
        $interHole->getInteraction()->getQuestion()->setUser($this->user);
        $interHole->getInteraction()->setType('InteractionHole');

        $html = $interHole->getHtml();
        $tabInput = explode('value="', $html);

        for( $i= 1; $i < count($tabInput); $i++)
        {
            $input = explode('"', $tabInput[$i]);
            $regExpr = 'value="'.$input[0].'"';
            $html = str_replace($regExpr, 'value=""', $html);
        }
        
        $interHole->setHtmlWhithoutValue($html);
        $this->em->persist($interHole);
        $this->em->persist($interHole->getInteraction()->getQuestion());
        $this->em->persist($interHole->getInteraction());

        $ord = 1;
        foreach($interHole->getHoles() as $hole)
        {
            foreach($hole->getWordResponses() as $word)
            {
                $hole->addWordResponse($word);
                $this->em->persist($word);
            }
            $hole->setPosition($ord);
            $interHole->addHole($hole);
            $this->em->persist($hole);
            $ord = $ord+1;
        }
        
        //On persite tous les hints de l'entité interaction
        foreach($interHole->getInteraction()->getHints() as $hint)
        {
            $interHole->getInteraction()->addHint($hint);
            $this->em->persist($hint);
        }

        $this->em->flush();

        if($this->exercise != -1)
        {
            $exo = $this->em->getRepository('UJMExoBundle:Exercise')->find($this->exercise);
            $EQ = new ExerciseQuestion($exo,$interHole->getInteraction()->getQuestion());

            $dql = 'SELECT max(eq.ordre) FROM UJM\ExoBundle\Entity\ExerciseQuestion eq '
                . 'WHERE eq.exercise='.$this->exercise;
            $query = $this->em->createQuery($dql);
            $maxOrdre = $query->getResult();

            $EQ->setOrdre((int)$maxOrdre[0][1]+1);
            $this->em->persist($EQ);

            $this->em->flush();
        }

    }
    
    public function processUpdate(InteractionHole $originalInterHole)
    {
        $originalHoles = array();
        $originalHints = array();
        
        // Create an array of the current Hole objects in the database
        foreach ($originalInterHole->getHoles() as $hole)
        {
            $originalHoles[] = $hole;
        }
        foreach ($originalInterHole->getInteraction()->getHints() as $hint)
        {
            $originalHints[] = $hint;
        }
        
        if( $this->request->getMethod() == 'POST' )
        {
            $this->form->bindRequest($this->request);

            if( $this->form->isValid() )
            {
                $this->onSuccessUpdate($this->form->getData(), $originalHoles, $originalHints);
                return true;
            }
        }

        return false;
    }
    
    private function onSuccessUpdate(InteractionHole $interHole, $originalHoles, $originalHints)
    {
        // filter $originalHoles to contain hole no longer present
        foreach ($interHole->getHoles() as $hole)
        {
            $this->delWord($hole, $originalHoles);
            foreach ($originalHoles as $key => $toDel)
            {
                if ($toDel->getId() === $hole->getId())
                {
                    unset($originalHoles[$key]);
                }
            }
        }

        // remove the relationship between the hole and the interactionhole
        foreach ($originalHoles as $hole)
        {            
            // remove the hole from the interactionhole
            $interHole->getHoles()->removeElement($hole);

            // if you wanted to delete the Hole entirely, you can also do that
            $this->em->remove($hole);
        }
            
       
        // filter $originalHints to contain hint no longer present
        foreach($interHole->getInteraction()->getHints() as $hint)
        {
            foreach ($originalHints as $key => $toDel)
            {
                if ($toDel->getId() === $hint->getId())
                {
                    unset($originalHints[$key]);
                }
            }
        }

        // remove the relationship between the hint and the interactionhole
        foreach ($originalHints as $hint)
        {
            // remove the Hint from the interactionhole
            $interHole->getInteraction()->getHints()->removeElement($hint);

            // if you wanted to delete the Hint entirely, you can also do that
            $this->em->remove($hint);
        }            
       
        $html = $interHole->getHtml();
        $tabInput = explode('value="', $html);

        for( $i= 1; $i < count($tabInput); $i++)
        {
            $input = explode('"', $tabInput[$i]);
            $regExpr = 'value="'.$input[0].'"';
            $html = str_replace($regExpr, 'value=""', $html);
        }
        
        $interHole->setHtmlWhithoutValue($html);
        $this->em->persist($interHole);
        $this->em->persist($interHole->getInteraction()->getQuestion());
        $this->em->persist($interHole->getInteraction());
        
        $ord = 1;
        // On persiste tous les trous de l'interaction Hole.
        foreach($interHole->getHoles() as $hole)
        {
            foreach($hole->getwordResponses() as $word)
            {
                $hole->addWordResponse($word);
                $this->em->persist($word);
            }
            $hole->setPosition($ord);
            $interHole->addHole($hole);
            $this->em->persist($hole);
            $ord++;
        }

        //On persite tous les hints de l'entité interaction
        foreach($interHole->getInteraction()->getHints() as $hint)
        {
            $interHole->getInteraction()->addHint($hint);
            $this->em->persist($hint);
        }
            
        $this->em->flush();

    }
    
    private function delWord($hole, $originalHoles)
    {
        $wordResponses = $hole->getWordResponses()->toArray();

        foreach($originalHoles as $holeOrig)
        {
            $originalWords = $holeOrig->getwordResponses()->getSnapshot();
            if($hole->getId() === $holeOrig->getId())
            {
                foreach($wordResponses as $word)
                {
                    foreach($originalWords as $key => $toDel)
                    {
                        if ($toDel->getId() === $word->getId())
                        {
                            unset($originalWords[$key]);
                        }
                    }
                }
                
                // remove the relationship between the hole and the interactionhole
                foreach ($originalWords as $word)
                {
                    // remove the hole from the interactionhole
                    $hole->getWordResponses()->removeElement($word);

                    // if you wanted to delete the Hole entirely, you can also do that
                    $this->em->remove($word);
                }
            
            }
        }
    }
}
