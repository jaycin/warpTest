<?php
namespace App\Controller;

use App\Entity\Task;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;




class TaskController extends Controller
{
    /**
     * @Route("/" ,name="home")
     * @method({"GET"})
     */
    public function index(\Swift_Mailer $mailer)
    {
        $tasks = $this->getDoctrine()
            ->getRepository(Task::class)
            ->findBy(array("isDeleted"=>false),array('dueDate'=>"DESC"));

        if(!empty($tasks)) {
            $useremail = "test@mail.com"; // assumed there would be a login in real case
            $today = date("Y-m-d");
            foreach ($tasks as $k => $v) {
                if ($today == $v->getDueDate()->format("Y-m-d")) {
                    $message = (new \Swift_Message('Task '.$v->getTitle()." is Due"))
                        ->setFrom($useremail)
                        ->setTo('')
                        ->setBody($v->getBody(),
                            'text/html'
                        );

                    $mailer->send($message);
                }
            }
        }
        return $this->render('tasks/index.html.twig', array("tasks" => $tasks));
    }

    /**
 * @Route("/Task/delete")
 * @method({"POST"})
 */
    public function delete(Request $request)
    {
        $id = $request->request->get('id');
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);

        $task->setIsDeleted(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->merge($task);
        $entityManager->flush();
        die();
    }

    /**
     * @Route("/Task/complete")
     * @method({"POST"})
     */
    public function complete(Request $request)
    {
        $id = $request->request->get('id');
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);

        $task->setIsComplete(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->merge($task);
        $entityManager->flush();
        die();
    }

    private function getForm($task)
    {
        return $this->createFormBuilder($task)
            ->add('title',TextType::class,array('attr'=>array('class'=>'form-control')))
            ->add('body',TextareaType::class,array('attr'=>array('class'=>'form-control')))
            ->add('dueDate',DateType::class,array('attr'=>array('class'=>'form-control')))
            ->add('priority',ChoiceType::class,array('choices'=>["Low"=>false,"High"=>true],'attr'=>array('class'=>'form-control')) )
            ->add('save',SubmitType::class,array('label'=>'Submit','attr'=>array('class'=>'btn btn-primary mt-3')))
            ->getForm();
    }

    /**
     * @Route("/Task/new")
     * @method({"GET","POST"})
     */
    public function new(Request $request)
    {
        $task = new Task();

        $form =$this->getForm($task);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $task = $form->getData();
            $task->setIsDeleted(0);
            $task->setIsComplete(0);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('tasks/new.html.twig',array("form"=>$form->createView()));
    }

    /**
     * @Route("/Task/edit/{id}")
     * @method({"GET","POST"})
     */
    public function editTask($id,Request $request)
    {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);
        $form =$this->getForm($task);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $task = $form->getData();
            $task->setIsDeleted(0);
            $task->setIsComplete(0);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->merge($task);
            $entityManager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('tasks/new.html.twig',array("form"=>$form->createView()));

    }

    /**
     * @Route("/")
     * @method({"POST"})
     */
    public function save()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $task = new Task();

        $entityManager->persist($task);
        $entityManager->flush();

        $task->getId();

    }
}