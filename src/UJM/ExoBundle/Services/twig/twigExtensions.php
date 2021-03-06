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

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace UJM\ExoBundle\Services\twig;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Request;

class TwigExtensions extends \Twig_Extension
{
    protected $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine  = $doctrine;
    }

    public function getName()
    {
        return "twigExtensions";
    }

    public function getFunctions()
    {

        return array(
            'regexTwig'            => new \Twig_Function_Method($this, 'regexTwig'),
            'getInterTwig'         => new \Twig_Function_Method($this, 'getInterTwig'),
        );

    }

    public function regexTwig($pattern, $str)
    {
        //return int
        return preg_match((string) $pattern, (string) $str);
    }

    public function getInterTwig($inter_id, $type_inter)
    {
        //$em = $this->doctrine->getEntityManager();

        switch ($type_inter)
        {
            case "InteractionQCM":
                //$interQCM = $em->getRepository('UJMExoBundle:InteractionQCM')->find($inter_id);
                $interQCM = $this->doctrine
                                 ->getEntityManager()
                                 ->getRepository('UJMExoBundle:InteractionQCM')
                                 ->getInteractionQCM($inter_id);
                $inter['question'] = $interQCM[0];
                $inter['maxScore'] = $this->getQCMScoreMax($interQCM[0]);
                //$inter = $em->getRepository('UJMExoBundle:InteractionQCM')->findOneBy(array('id' => $inter_id));
            break;

            case "InteractionGraphic":

            break;

            case "InteractionHole":

            break;

            case "InteractionOpen":

            break;
        }

        return $inter;
    }

    private function getQCMScoreMax($interQCM)
    {
        $scoreMax = 0;
        if($interQCM->getWeightResponse() == 1)
        {
            foreach($interQCM->getChoices() as $choice)
            {
                if($choice->getRightResponse() == 1)
                {
                    $scoreMax += $choice->getWeight();
                }
            }
        }
        else
        {
            $scoreMax = $interQCM->getScoreRightResponse();
        }
        return $scoreMax;
    }

}