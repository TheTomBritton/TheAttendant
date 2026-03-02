<?php namespace ProcessWire;

/**
 * Template: contact.php
 * Contact form using FrontendForms module.
 * Fields: title, body, summary, seo_title, seo_description
 *
 * Requires: FrontendForms module installed and active.
 */

$content = renderBreadcrumbs($page);
$content .= "<h1>{$page->title}</h1>";

if ($page->body) {
    $content .= "<div class='page-intro'>{$page->body}</div>";
}

// Build contact form with FrontendForms
$form = new \FrontendForms\Form('contact-form');
$form->setMinTime(3);      // Spam: must take at least 3 seconds
$form->setMaxTime(3600);   // Spam: must complete within 1 hour

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
    // Send the email
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

    $content .= "<div class='success-message'>";
    $content .= "<p>Thank you for your message. We'll get back to you shortly.</p>";
    $content .= "</div>";
} else {
    $content .= "<div class='contact-form'>";
    $content .= $form->render();
    $content .= "</div>";
}
