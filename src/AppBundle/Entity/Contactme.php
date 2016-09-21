<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints as CaptchaAssert;

class Contactme
{
    /**
     * @Assert\NotBlank(message = "Поле контактов - не может быть пустым")
     * @Assert\Length(min=10, minMessage = "Сообщение слишком короткое, минимум 10 символов!",
     *      max=5000, maxMessage = "Сообщение слишком длинное, максимум 5000 символов!")
     */
    public $subject;
    /**
     * @Assert\NotBlank(message = "Поле контактов - не может быть пустым")
     * @Assert\Length(min=3, minMessage = "Имя слишком короткое, минимум 3 символа!",
     *      max=100, maxMessage = "Имя слишком длинное, максимум 100 символов!")
     */
    public $name;
    /**
     * @Assert\NotBlank(message = "Поле контактов - не может быть пустым")
     * @Assert\Length(min=6, minMessage = "Поле контакт слишком короткое, минимум 6 символов!",
     *      max=50, maxMessage = "Поле контакт слишком длинное, максимум 50 символов!")
     * @Assert\Regex(pattern="/(?:\d)?\d+/", message="Поле телефона должно содержать номер!")
     */
    public $contact;
    /**
     * @CaptchaAssert\ValidCaptcha(
     *      message = "Капча введена не верно."
     * )
     */
    protected $captchaCode;

    public function getCaptchaCode()
    {
        return $this->captchaCode;
    }

    public function setCaptchaCode($captchaCode)
    {
        $this->captchaCode = $captchaCode;
    }

    public function __construct($subject = null, $name = null, $contact = null, $captchaCode = null)
    {
        $this->subject = $subject;
        $this->name = $name;
        $this->contact = $contact;
        $this->captchaCode = $captchaCode;
    }
}