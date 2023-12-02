<?php

namespace App\Controller\Api;

use App\Entity\TopTen;
use App\Repository\TopTenRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopTenController extends AbstractController
{
    // Get TopTen by id
    // Get TopTen by round id
    // Get predictions by TopTen id
    // Create TopTen
    // Update TopTen deadline, results
    // Delete TopTen
}