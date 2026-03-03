<?php namespace ProcessWire;

/**
 * Template: contact.php
 * Contact form using FrontendForms module. Filix contact layout.
 * Fields: title, body, summary, seo_title, seo_description
 *
 * Requires: FrontendForms module installed and active.
 */

// Banner
$subtitle = $page->summary ?: '';
$hero = renderInnerBanner($page->title, $subtitle, 'contact_banner');

// Content — contact form in Filix layout
$content = "<section class='contact_form_wrap'>";
$content .= "<div class='container'>";
$content .= "<div class='row'>";

if ($page->body) {
    $content .= "<div class='col-12'>";
    $content .= "<div class='wow fadeInUp'>{$page->body}</div>";
    $content .= "</div>";
}

$content .= "<div class='col-12'>";

// Build contact form with FrontendForms
$form = new \FrontendForms\Form('contact-form');
$form->setMinTime(3);
$form->setMaxTime(3600);

// Name
$name = new \FrontendForms\InputText('name');
$name->setLabel('Your Name');
$name->setRule('required');
$form->add($name);

// Email
$email = new \FrontendForms\InputEmail('email');
$email->setLabel('Email Address');
$email->setRule('required');
$email->setRule('email');
$form->add($email);

// Subject
$subject = new \FrontendForms\InputText('subject');
$subject->setLabel('Subject');
$subject->setRule('required');
$form->add($subject);

// Message
$message = new \FrontendForms\Textarea('message');
$message->setLabel('Message');
$message->setRule('required');
$form->add($message);

// Submit button
$submit = new \FrontendForms\Button('submit');
$submit->setAttribute('value', 'Send Message');
$form->add($submit);

if ($form->isValid()) {
    $mail = wireMail();
    $mail->to($config->adminEmail ?: 'admin@example.com');
    $mail->from($form->getValue('email'));
    $mail->subject('Contact: ' . $sanitizer->text($form->getValue('subject')));
    $mail->body(
        "Name: " . $sanitizer->text($form->getValue('name')) . "\n" .
        "Email: " . $sanitizer->email($form->getValue('email')) . "\n\n" .
        $sanitizer->textarea($form->getValue('message'))
    );
    $mail->send();

    $content .= "<div class='success-message wow fadeInUp'>";
    $content .= "<p>Thank you for your message. We'll get back to you shortly.</p>";
    $content .= "</div>";
} else {
    $content .= "<div class='wow fadeInUp'>";
    $content .= $form->render();
    $content .= "</div>";
}

$content .= "</div>"; // .col-12
$content .= "</div>"; // .row
$content .= "</div>"; // .container
$content .= "</section>";

// No sidebar for contact
$sidebar = '';
