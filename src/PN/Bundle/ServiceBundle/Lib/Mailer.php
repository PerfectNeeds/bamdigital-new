<?php

namespace PN\Bundle\ServiceBundle\Lib;

/**
 * Mailer
 * Using Swift Mailer or external service for send emails
 * 
 * <pre><code>
 * $mailer = Mailer::newInstance()
 * ->setSubject('Test')
 * ->setTo('exampe@exampe.com')
 * ->setFrom('exampe@exampe.com')
 * ->setBody('test')
 * ->setAttach('/path/to/example.pdf');
 * $mailer->send();
 * </code></pre>
 * @version 1.0
 * @author Peter Nassef <peter.nassef@gmail.com>
 */
class Mailer {

    const swiftMailer = false;

    /**
     * @var Swift_Mailer 
     */
    protected $mailer;
    protected $subject = NULL;
    protected $from = NULL;
    protected $to = NULL;
    protected $body = NULL;
    protected $bcc = NULL;
    protected $cc = NULL;
    protected $attach = NULL;
    protected $contentType = NULL;

    public function __construct() {
        global $kernel;
        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $this->mailer = $kernel->getContainer()->get('mailer');
    }

    public static function newInstance() {
        return new self;
    }

    public function send() {
        $this->dataValidation();
        if (self::swiftMailer) {
            $this->sendBySwiftMailer();
        } else {
            $this->sendByExternalService();
        }

        return TRUE;
    }

    /**
     * Check the email parameters are valid
     * @throws \Exception
     */
    private function dataValidation() {
        if (!$this->subject) {
            throw new \Exception("Cannot send message without a subject");
        }
        if (!$this->from) {
            throw new \Exception("Cannot send message without a sender address");
        }
        if (!$this->to) {
            throw new \Exception("Cannot send message without TO email");
        }
        if (!$this->body) {
            throw new \Exception("Cannot send message without body");
        }
        if ($this->attach != NULL AND ! file_exists($this->attach)) {
            throw new \Exception("Attached file is not found");
        }
    }

    private function sendBySwiftMailer() {
        $message = \Swift_Message::newInstance()
                ->setSubject($this->subject)
                ->setFrom($this->from)
                ->setTo($this->to)
                ->setBody($this->body, $this->contentType);

        if ($this->bcc != NULL) {
            $message->setBcc($this->bcc);
        }
        if ($this->cc != NULL) {
            $message->setCc($this->cc);
        }

        if ($this->attach != NULL) {
            $message->attach(\Swift_Attachment::fromPath($this->getAttach()));
        }
        $this->mailer->send($message);
    }

    private function sendByExternalService() {
        $message = array(
            'subject' => $this->getSubject(),
            'from' => $this->getFrom(),
            'to' => $this->getTo(),
            'body' => $this->getBody()
        );
        if ($this->attach != NULL) {
            $fileExtension = pathinfo($this->getAttach(), PATHINFO_EXTENSION);
            $base64Enc = chunk_split(base64_encode(file_get_contents($this->getAttach())));
            $message['attach'] = array('encode' => $base64Enc, 'ext' => $fileExtension);
        }
        \PN\Utils\Mailer::sendEmail($message);
    }

    /**
     * Get the To addresses of this message.
     *
     * @return array
     */
    public function getTo() {
        return $this->to;
    }

    /**
     * Set the to addresses of this message.
     *
     * If multiple recipients will receive the message an array should be used.
     * Example: array('receiver@domain.org', 'other@domain.org' => 'A name')
     *
     * If $name is passed and the first parameter is a string, this name will be
     * associated with the address.
     *
     * @param mixed  $addresses
     * @param string $name      optional
     *
     * @return $this
     */
    public function setTo($addresses, $name = null) {
        if (!is_array($addresses) && isset($name)) {
            $addresses = array($addresses => $name);
        }

        if (!$this->to) {
            $this->to = (array) $addresses;
        }

        return $this;
    }

    /**
     * Get the subject of this message.
     *
     * @return string
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * Set the subject of this message.
     *
     * @param string $subject
     *
     * @return $this
     */
    public function setSubject($subject) {
        if (!$this->subject) {
            $this->subject = $subject;
        }

        return $this;
    }

    /**
     * Get the from address of this message.
     *
     * @return mixed
     */
    public function getFrom() {
        return $this->from;
    }

    /**
     * Set the from address of this message.
     *
     * You may pass an array of addresses if this message is from multiple people.
     *
     * If $name is passed and the first parameter is a string, this name will be
     * associated with the address.
     *
     * @param string|array $addresses
     * @param string       $name      optional
     *
     * @return $this
     */
    public function setFrom($addresses, $name = null) {
        if (!is_array($addresses) && isset($name)) {
            $addresses = array($addresses => $name);
        }

        if (!$this->from) {
            $this->from = (array) $addresses;
        }

        return $this;
    }

    /**
     * Get the body of this entity as a string.
     *
     * @return string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Set the body of this entity, either as a string.
     *
     * @param mixed  $body
     *
     * @return $this
     */
    public function setBody($body) {

        $this->body = $body;
        if ($body != strip_tags($body)) {
            $this->setContentType('text/html');
        } else {
            $this->setContentType('text/plain');
        }

        return $this;
    }

    /**
     * Get the Bcc addresses of this message.
     *
     * @return array
     */
    public function getBcc() {
        return $this->bcc;
    }

    /**
     * Set the Bcc addresses of this message.
     *
     * If $name is passed and the first parameter is a string, this name will be
     * associated with the address.
     *
     * @param mixed  $addresses
     * @param string $name      optional
     *
     * @return $this
     */
    public function setBcc($addresses, $name = null) {
        if (!is_array($addresses) && isset($name)) {
            $addresses = array($addresses => $name);
        }

        if (!$this->bcc) {
            $this->bcc = (array) $addresses;
        }

        return $this;
    }

    /**
     * Get the Cc address of this message.
     *
     * @return array
     */
    public function getCc() {
        return $this->cc;
    }

    /**
     * Set the Cc addresses of this message.
     *
     * If $name is passed and the first parameter is a string, this name will be
     * associated with the address.
     *
     * @param mixed  $addresses
     * @param string $name      optional
     *
     * @return $this
     */
    public function setCc($addresses, $name = null) {
        if (!is_array($addresses) && isset($name)) {
            $addresses = array($addresses => $name);
        }

        if (!$this->cc) {
            $this->cc = (array) $addresses;
        }

        return $this;
    }

    public function getContentType() {
        return $this->contentType;
    }

    public function setContentType($contentType) {
        $this->contentType = $contentType;
        return $this;
    }

    function getAttach() {
        return $this->attach;
    }

    /**
     * @param string $attach file path
     */
    function setAttach($attach) {
        $this->attach = $attach;
        return $this;
    }

}
