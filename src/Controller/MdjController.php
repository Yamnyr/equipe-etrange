<?php

namespace App\Controller;

use App\Entity\Historique;
use App\Entity\User;
use App\Repository\ClasseRepository;
use App\Repository\HistoriqueRepository;
use App\Repository\MissionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MdjController extends AbstractController
{
    #[Route('/mdj', name: 'app_mdj')]
    public function index(Security $security, MissionRepository $missionRepository, EntityManagerInterface $entityManager, HistoriqueRepository $historiqueRepository): Response
    {
        $user = $this->getUser();

        $classe = $user ? $user->getClasse() : null;
        $mdj = $classe ? $classe->getMdj() : null;
        $dateajout = $classe ? $classe->getDateAjout() : null;

        if ($mdj === null || (new \DateTime())->diff($dateajout)->s > 60) {


            $hist = $historiqueRepository->findBy(['user' => $user]);
            //si la mission du jour n'est pas set
            if ($classe !== null && $classe->getMdj() !== null) {
//dd($historiqueRepository->doesEntryExist($user, $classe->getMdj(), $classe->getDateAjout()));
                var_dump(!$historiqueRepository->doesEntryExist($user, $classe->getMdj(), $classe->getDateAjout()));
                if (empty($hist) || !$historiqueRepository->doesEntryExist($user, $classe->getMdj(), $classe->getDateAjout())) {
                    $historique = new Historique();

                    $historique->setUser($user);
                    $historique->setMission($classe->getMdj());
                    $historique->setDateAjoutMdj($classe->getDateAjout());
                    $historique->setResultat(False);

                    $entityManager->persist($historique);

                    $user->setPv($user->getPv() - 1);
                    $entityManager->persist($user);

                    $entityManager->flush();

                    $this->addFlash('réussit', "tu n'as validé ta mission à temps");
                }
            }

            $relatedMissions = $missionRepository->findBy(['classe' => $classe]);
            if (!empty($relatedMissions)) {
                $randomKey = array_rand($relatedMissions);
                $randomMission = $relatedMissions[$randomKey];

                $classe->setMdj($randomMission);
                $classe->setDateAjout(new \DateTime());
                $entityManager->persist($classe);
                $entityManager->flush();
                $this->addFlash('réussit', "mission mis à jour");
            }
        }


        return $this->render('mdj/index.html.twig', [
            'classe' => $classe,
        ]);
    }

    #[Route('/mdj/{id}/valide', name: 'app_mdj_valid')]
    public function add($id, MissionRepository $missionRepository, EntityManagerInterface $entityManager, ClasseRepository $classeRepository, HistoriqueRepository $historiqueRepository): Response
    {
        $user = $this->getUser();

        $classe = $user ? $user->getClasse() : null;
        var_dump(!$historiqueRepository->doesEntryExist($user, $classe->getMdj(), $classe->getDateAjout()));
        if (!$historiqueRepository->doesEntryExist($user, $classe->getMdj(), $classe->getDateAjout())) {
            $classe = $classeRepository->find($id);
            $historique = new Historique();

            $historique->setUser($user);
            $historique->setMission($classe->getMdj());
            $historique->setDateAjoutMdj($classe->getDateAjout());
            $historique->setResultat(True);

            $entityManager->persist($historique);
            $entityManager->flush();
            $this->addFlash('réussit', "La mission a été validé");
        }
        return $this->redirectToRoute('app_mdj', [], Response::HTTP_SEE_OTHER);
    }
}
