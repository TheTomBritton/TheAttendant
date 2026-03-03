<?php namespace ProcessWire;

/**
 * Template: contact.php
 * Contact page with Filix contact form styling.
 * Falls back to a basic HTML form if FrontendForms isn't installed.
 * Fields: title, body, summary
 */

// Inner banner
$hero = renderInnerBanner($page->title, $page->summary ?: '');

$content = "<div class='contact_form_wrap'>";
$content .= "<div class='container'>";

$content .= renderBreadcrumbs($page);

if ($page->body) {
    $content .= "<div class='blog_single_content'><div class='blog_single_item'>{$page->body}</div></div>";
}

// Check if FrontendForms module is available
if ($modules->isInstalled('FrontendForms')) {
    $form = new \FrontendForms\Form('contact');
    $form->setMinTime(3);
    $form->setMaxTime(3600);

    $name = new \FrontendForms\InputText('name');
    $name->setLabel('Your Name');
    $name->setRule('required');
    $form->add($name);

    $email = new \FrontendForms\InputEmail('email');
    $email->setLabel('Email Address');
    $email->setRule('required');
    $email->setRule('email');
    $form->add($email);

    $phone = new \FrontendForms\InputText('phone');
    $phone->setLabel('Phone Number (optional)');
    $form->add($phone);

    $subject = new \FrontendForms\InputText('subject');
    $subject->setLabel('Subject');
    $subject->setRule('required');
    $form->add($subject);

    $message = new \FrontendForms\Textarea('message');
    $message->setLabel('Your Message');
    $message->setRule('required');
    $form->add($message);

    $submit = new \FrontendForms\Button('submit');
    $submit->setAttribute('value', 'Send Message');
    $form->add($submit);

    if ($form->isValid()) {
        $mail = wireMail();
        $mail->to($config->adminEmail);
        $mail->from($form->getElementByName('email')->getValue());
        $mail->subject('Contact: ' . $form->getElementByName('subject')->getValue());
        $mail->body(
            "Name: " . $form->getElementByName('name')->getValue() . "\n" .
            "Email: " . $form->getElementByName('email')->getValue() . "\n" .
            "Phone: " . $form->getElementByName('phone')->getValue() . "\n\n" .
            $form->getElementByName('message')->getValue()
        );
        $mail->send();
    }

    $content .= "<div class='contact_form'>";
    $content .= $form->render();
    $content .= "</div>";

} else {
    // Fallback: basic HTML form with CSRF protection
    $formSent = false;

    if ($input->post->submit_contact) {
        $session->CSRF->validate();

        $contactName = $sanitizer->text($input->post->name);
        $contactEmail = $sanitizer->email($input->post->email);
        $contactSubject = $sanitizer->text($input->post->subject);
        $contactMessage = $sanitizer->textarea($input->post->message);

        if ($contactName && $contactEmail && $contactSubject && $contactMessage) {
            $mail = wireMail();
            $mail->to($config->adminEmail);
            $mail->from($contactEmail);
            $mail->subject("Contact: {$contactSubject}");
            $mail->body("Name: {$contactName}\nEmail: {$contactEmail}\n\n{$contactMessage}");
            $mail->send();

            $formSent = true;
        }
    }

    if ($formSent) {
        $content .= "<div class='alert_success'>";
        $content .= "<p><strong>Thank you for your message!</strong></p>";
        $content .= "<p>We'll get back to you as soon as possible.</p>";
        $content .= "</div>";
    } else {
        $content .= "<div class='contact_form'>";
        $content .= "<form method='post'>";
        $content .= $session->CSRF->renderInput();

        $content .= "<div class='row'>";
        $content .= "<div class='col-md-6'>";
        $content .= "<div class='form-group'>";
        $content .= "<input type='text' name='name' class='form-control' placeholder='Your Name *' required>";
        $content .= "</div>";
        $content .= "</div>";

        $content .= "<div class='col-md-6'>";
        $content .= "<div class='form-group'>";
        $content .= "<input type='email' name='email' class='form-control' placeholder='Email Address *' required>";
        $content .= "</div>";
        $content .= "</div>";
        $content .= "</div>"; // .row

        $content .= "<div class='form-group'>";
        $content .= "<input type='text' name='subject' class='form-control' placeholder='Subject *' required>";
        $content .= "</div>";

        $content .= "<div class='form-group'>";
        $content .= "<textarea name='message' class='form-control' placeholder='Your Message *' required></textarea>";
        $content .= "</div>";

        $content .= "<div class='form-group'>";
        $content .= "<button type='submit' name='submit_contact' value='1' class='sibmit_btn'>Send Message</button>";
        $content .= "</div>";

        $content .= "</form>";
        $content .= "</div>"; // .contact_form
    }
}

$content .= "</div>"; // .container
$content .= "</div>"; // .contact_form_wrap
