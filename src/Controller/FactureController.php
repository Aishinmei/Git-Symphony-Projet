<?php

namespace App\Controller;

use App\Entity\Factures;
use App\Repository\FacturesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class FactureController extends AbstractController
{
    #[Route('/facture', name: 'app_facture')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/FactureController.php',
        ]);
    }

    #[Route('/create_facture', methods: ['POST'])]
    public function createFacture(Request $request, EntityManagerInterface $em): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $facture = new Factures();
        $facture->setAmount($data['amount']);
        $facture->setDueDate(new \DateTime($data['due_date']));
        $facture->setCustomerEmail($data['customer_email']);
        $em->persist($facture);
        $em->flush();
        return $this->json(['status' => 'Votre Facture est disponible!!'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/update_facture/{id}', methods: ['PUT'])]
    public function updateFacture(int $id, Request $request, EntityManagerInterface $em, FacturesRepository $factureRepository, LoggerInterface $logger): JsonResponse {
        $logger->info('PUT /update_facture called', ['method' => $request->getMethod(), 'content' => $request->getContent()]);
        try {
            $facture = $factureRepository->find($id);

            if (!$facture) {
                return $this->json(['status' => 'Facture inexistante!'], JsonResponse::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['amount'])) {
                $facture->setAmount($data['amount']);
            }

            if (isset($data['due_date'])) {
                $facture->setDueDate(new \DateTime($data['due_date']));
            }

            if (isset($data['customer_email'])) {
                $facture->setCustomerEmail($data['customer_email']);
            }

            $em->flush();

            return $this->json(['status' => 'Facture mise a jour!'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            $logger->error('An error occurred', ['exception' => $e]);
            return $this->json(['status' => 'Erreur', 'message' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/delete_facture/{id}', methods: ['DELETE'])]
    public function deleteFacture(int $id, EntityManagerInterface $em, FacturesRepository $factureRepository, LoggerInterface $logger): JsonResponse {
        $logger->info('DELETE /delete_facture called', ['method' => $request->getMethod()]);
        try {
            $facture = $factureRepository->find($id);

            if (!$facture) {
                return $this->json(['status' => 'Facture inexistante!'], JsonResponse::HTTP_NOT_FOUND);
            }

            $em->remove($facture);
            $em->flush();

            return $this->json(['status' => 'Facture supprimée!'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            $logger->error('An error occurred', ['exception' => $e]);
            return $this->json(['status' => 'Erreur', 'message' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
