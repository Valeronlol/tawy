<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Contactme;

class MainController extends Controller
{
    /**
     * @var array service array
     */
    private $data = array(
        'title' => 'Valeron',
        'descr' => 'Портфолио и блог Кузиванова Валерия. Статьи и заметки о веб разработке, верстке, HTML, CSS, JavaScript, фреймворках и front end трендах.',
        'keywords' => 'Кузиванов Валерий, портфолио, заказать сайт, front end developer, фронтенд разработчик, разработка сайтов, блог веб разработчика, создать интернет магазин',
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

    public function indexAction(Request $request)
    {
        //captcha creating
        $task = new Contactme();
        $task->setCaptchaCode('');
        $form = $this->createFormBuilder($task)
            ->add('captchaCode', 'Captcha\Bundle\CaptchaBundle\Form\Type\CaptchaType', array(
                'captchaConfig' => 'LoginCaptcha'))
            ->getForm();
        $this->setData(array('captcha' => $form->createView()));

        // send message e-mail button handler
        if( isset($_POST['btnsubmit']) ){
            $subject = $_POST['text'];
            $name = $_POST['name'];
            $contact = $_POST['phone'];
            $captcha_enter = $_POST['form']['captchaCode'];

            //validation entered information
            $form_valid = new Contactme($subject, $name, $contact, $captcha_enter);
            $validator = $this->get('validator');
            $errors = $validator->validate($form_valid);
            $this->setData(array('errors' => $errors));

            //error handler
            if (count($errors) > 0) {
                return $this->render('common/validation.html.twig', $this->getData());
            }
            else{
                $this->sendMail($subject, $name, $contact);
                $this->setData(array('email_status' => 'Сообщение отправлено.'));
            }
        }
        return $this->render( "base.html.twig", $this->getData());
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
            return true;
        else
            return false;
    }
}
