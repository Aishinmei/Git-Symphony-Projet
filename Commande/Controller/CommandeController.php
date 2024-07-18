<?php 

// src/Controller/CommandeController.php
namespace App\Controller;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    #[Route('/commande/create', name: 'create_commande', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $commande = new Commande();
        $commande->setProductId($data['product_id']);
        $commande->setCustomerEmail($data['customer_email']);
        $commande->setQuantity($data['quantity']);
        $commande->setTotalPrice($data['total_price']);

        $entityManager->persist($commande);
        $entityManager->flush();

        // Envoi de la requête à Billing Service
        try {
            $response = $this->client->request('POST', 'http://billing-service.local/create-invoice', [
                'json' => [
                    'amount' => $data['total_price'],
                    'due_date' => (new \DateTime('+30 days'))->format('Y-m-d'),
                    'customer_email' => $data['customer_email']
                ],
            ]);

            if ($response->getStatusCode() !== 201) {
                return new JsonResponse(['status' => 'Failed to create invoice'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'Error: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['status' => 'Commande created!'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/commande/read', name: 'read_commande', methods: ['POST'])]
    public function read(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $commande = $entityManager->getRepository(Commande::class)->find($data['id']);

        if (!$commande) {
            return new JsonResponse(['status' => 'Commande not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $commande->getId(),
            'product_id' => $commande->getProductId(),
            'customer_email' => $commande->getCustomerEmail(),
            'quantity' => $commande->getQuantity(),
            'total_price' => $commande->getTotalPrice(),
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/commande/update', name: 'update_commande', methods: ['POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $commande = $entityManager->getRepository(Commande::class)->find($data['id']);

        if (!$commande) {
            return new JsonResponse(['status' => 'Commande not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $commande->setProductId($data['product_id']);
        $commande->setCustomerEmail($data['customer_email']);
        $commande->setQuantity($data['quantity']);
        $commande->setTotalPrice($data['total_price']);

        $entityManager->flush();

        return new JsonResponse(['status' => 'Commande updated!'], JsonResponse::HTTP_OK);
    }

    #[Route('/commande/delete', name: 'delete_commande', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $commande = $entityManager->getRepository(Commande::class)->find($data['id']);

        if (!$commande) {
            return new JsonResponse(['status' => 'Commande not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($commande);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Commande deleted!'], JsonResponse::HTTP_OK);
    }
}

?>
