<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Contactme;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainController extends Controller
{
    /**
     * @var array service array
     */
    private $data = array(
        'title' => 'Valeron',
        'descr' => 'Портфолио и блог Кузиванова Валерия. Статьи и заметки о веб разработке, верстке, HTML, CSS, JavaScript, фреймворках и front end трендах.',
        'keywords' => 'Блог Кузиванова Валерия, веб разработка',
        'lang' => 'ru',
        'top_menu_buttons' => array( 'Портфолио', 'Обо мне', 'Контакты', 'Блог'),
    );

    /**
     * @return array get data from service array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $setParam Added data
     */
    public function setData($setParam)
    {
        $this->data = array_merge( $this->data, $setParam);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // Build captcha form
        $task = new Contactme();
        $form = $this->createFormBuilder($task)
            ->add('name', TextType::class, array('label' => false, 'attr' => array( 'placeholder' => 'Имя', 'class' => 'inp_cont') ))
            ->add('contact', TextType::class, array('label' => false, 'attr' => array( 'placeholder' => 'Телефон', 'class' => 'inp_cont') ))
            ->add('subject', TextareaType::class, array('label' => false, 'attr' => array( 'placeholder' => 'Сообщение') ))
            ->add('captchaCode', 'Captcha\Bundle\CaptchaBundle\Form\Type\CaptchaType', array(
                'captchaConfig' => 'LoginCaptcha', 'label' => false , 'attr' => array( 'placeholder' => 'КАПЧА') ))
            ->add('save', SubmitType::class, array('label' => 'Отправить'))
            ->getForm();
        $this->setData(array('captcha' => $form->createView()));
        $form->handleRequest($request);

        $subject = $request->get('form')['subject'];
        $name = $request->get('form')['name'];
        $contact = $request->get('form')['contact'];
        $captchaCode = $request->get('form')['captchaCode'];

        if ( $form->isSubmitted() && $form->isValid() ) // valid
        {
            $this->sendMail($subject, $name, $contact);
            $this->setData(array('email_status' => 'Сообщение отправлено.'));
            return $this->render( "base.html.twig", $this->getData());
        }

        if ( $form->isSubmitted() && !$form->isValid() ) // not valid
        {
            $form_valid = new Contactme( $name, $subject, $contact, $captchaCode);
            $validator = $this->get('validator');
            $errors = $validator->validate($form_valid);

            if (count($errors) > 0) //display errors
            {
                $this->setData(array('errors' => $errors));
                return $this->render('common/validation.html.twig', $this->getData());
            }
        }
        return $this->render( "base.html.twig", $this->getData());
    }

    /**
     * telegram handler
     */
    public function sendAction(Request $request)
    {
        $data = $request->request->all();
        $telegram = new TelegramController();
        $telegram->teleSend($data['message']);

        return new JsonResponse($data);
    }

    /**
     * Send mail handler
     * @param string $subject
     * @param string $name
     * @param string $contact
     *
     * @return bool
     */
    public function sendMail($subject, $name, $contact)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Email from blog')
            ->setFrom('valerii.kuzivanov@gmail.com')
            ->setTo('green-travel.kg@mail.ru')
            ->setBody(
                $this->renderView('email/message.html.twig',
                    array(
                        'name' => $name,
                        'contact' => $contact,
                        'subject' => $subject
                    )
                ),
                'text/html'
            );

        if ( $this->get('mailer')->send($message) )
        {
            return true;
        }
        return false;
    }
}
