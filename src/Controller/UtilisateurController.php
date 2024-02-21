<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Form\UtilisateurEditType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/utilisateur')]
class UtilisateurController extends AbstractController
{
    #[Route('/', name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }
    #[Route('/newAdmin', name: 'app_utilisateur_newAdmin', methods: ['GET', 'POST'])]
    public function newA(Request $request, UtilisateurRepository $utilisateurRepository ,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setRole('Admin');
        $form = $this->createForm(UtilisateurType::class, $utilisateur , ['user_role' => "Admin"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('ImagePath')->getData();

            if ($uploadedFile) {
                // generate a unique file name
                $newFileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
                $targetDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/images';
            
                // move the uploaded file to the target directory
                $uploadedFile->move(
                    $targetDirectory, // specify the target directory where the file should be saved
                    $newFileName      // specify the new file name
                );
                    
                            // set the image path to the path of the uploaded file
                            $utilisateur->setImagePath('uploads/images/' . $newFileName);
                // encode the plain password
                $utilisateur->setPassword(
                    $userPasswordHasher->hashPassword(
                        $utilisateur,
                        $form->get('Password')->getData()
                    )
                );
            }
            $utilisateurRepository->save($utilisateur, true);

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/newAdmin.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/newFreelancer', name: 'app_utilisateur_newFreelancer', methods: ['GET', 'POST'])]
    public function newF(Request $request, UtilisateurRepository $utilisateurRepository ,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setRole('Freelancer');
        $utilisateur->setRating(0);
        $utilisateur->setIsBanned(0);
        $utilisateur->setTotalJobs(0);
        $form = $this->createForm(UtilisateurType::class, $utilisateur , ['user_role' => "Freelancer"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('ImagePath')->getData();

            if ($uploadedFile) {
                // generate a unique file name
                $newFileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
                $targetDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/images';
            
                // move the uploaded file to the target directory
                $uploadedFile->move(
                    $targetDirectory, // specify the target directory where the file should be saved
                    $newFileName      // specify the new file name
                );
                    
                            // set the image path to the path of the uploaded file
                            $utilisateur->setImagePath('uploads/images/' . $newFileName);
                // encode the plain password
                $utilisateur->setPassword(
                    $userPasswordHasher->hashPassword(
                        $utilisateur,
                        $form->get('Password')->getData()
                    )
                );
            }
            $utilisateurRepository->save($utilisateur, true);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/newEntreprise', name: 'app_utilisateur_newEntreprise', methods: ['GET', 'POST'])]
    public function newE(Request $request, UtilisateurRepository $utilisateurRepository ,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setRole('Entreprise');
        $utilisateur->setIsBanned(0);
        $form = $this->createForm(UtilisateurType::class, $utilisateur , ['user_role' => "Entreprise"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('ImagePath')->getData();

if ($uploadedFile) {
    // generate a unique file name
    $newFileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
    $targetDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/images';

    // move the uploaded file to the target directory
    $uploadedFile->move(
        $targetDirectory, // specify the target directory where the file should be saved
        $newFileName      // specify the new file name
    );
        
                // set the image path to the path of the uploaded file
                $utilisateur->setImagePath('uploads/images/' . $newFileName);
                // encode the plain password
                $utilisateur->setPassword(
                    $userPasswordHasher->hashPassword(
                        $utilisateur,
                        $form->get('Password')->getData()
                    )
                );
            }
            $utilisateurRepository->save($utilisateur, true);

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
            'user_role' => 'Entreprise',
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/profile/{id}', name: 'app_utilisateur_showProfile', methods: ['GET'])]
    public function showFront(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/profile.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

   /* #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, UtilisateurRepository $utilisateurRepository): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur, ['user_role' => $utilisateur->getRole()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateurRepository->save($utilisateur, true);
            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }*/
    #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, UtilisateurRepository $utilisateurRepository): Response
    {
        $form = $this->createForm(UtilisateurEditType::class, $utilisateur, ['user_role' => $utilisateur->getRole()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('ImagePath')->getData();

            if ($uploadedFile) {
                // generate a unique file name
                $newFileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
                $targetDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/images';
            
                // move the uploaded file to the target directory
                $uploadedFile->move(
                    $targetDirectory, // specify the target directory where the file should be saved
                    $newFileName      // specify the new file name
                );
                    
                            // set the image path to the path of the uploaded file
                            $utilisateur->setImagePath('uploads/images/' . $newFileName);
                
            }
            $utilisateurRepository->save($utilisateur, true);
            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editProfile', name: 'app_utilisateur_editProfile', methods: ['GET', 'POST'])]
    public function editProfile(Request $request, Utilisateur $utilisateur, UtilisateurRepository $utilisateurRepository): Response
    {
        $form = $this->createForm(UtilisateurEditType::class, $utilisateur, ['user_role' => $utilisateur->getRole()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('ImagePath')->getData();

            if ($uploadedFile) {
                // generate a unique file name
                $newFileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
                $targetDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/images';
            
                // move the uploaded file to the target directory
                $uploadedFile->move(
                    $targetDirectory, // specify the target directory where the file should be saved
                    $newFileName      // specify the new file name
                );
                    
                            // set the image path to the path of the uploaded file
                            $utilisateur->setImagePath('uploads/images/' . $newFileName);
                
            }
            $utilisateurRepository->save($utilisateur, true);
            return $this->redirectToRoute('app_utilisateur_showProfile', ['id' => $utilisateur->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/editProfile.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $utilisateur, UtilisateurRepository $utilisateurRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $utilisateurRepository->remove($utilisateur, true);
        }

        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }

    
    #[Route("/verify", name : 'app_verify_email')]
    public function verifyUserEmail(): Response
    {
        // TODO
        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }


   
}
