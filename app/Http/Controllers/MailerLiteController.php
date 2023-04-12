<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use MailerLite\MailerLite;

/**
 * Controller for managing MailerLite API integration.
 */
class MailerLiteController extends Controller
{
    /**
     * Display the form for entering the API key.
     *
     * @return \Illuminate\View\View
     */
    public function showApiKeyForm()
    {
        $apiKey = ApiKey::first();

        if (!$apiKey) {
            return view('api_key_form',['api_key' => ""]);
        }

        return view('api_key_form',['api_key' => $apiKey->api_key]);
    }

    /**
     * Get an instance of the MailerLite API class.
     *
     * @return MailerLite|null
     */
    public function getMailerLiteInstance()
    {
        $apiKey = ApiKey::first();
        if (!$apiKey) {
            return null;
        }

        return new MailerLite(['api_key' => $apiKey->api_key]);
    }

    /**
     * Validate and save the API key entered in the form.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function validateAndSaveApiKey(Request $request)
    {
        $request->validate(
            ['api_key' => 'required',]
        );
    
        $apiKey = $request->input('api_key');
    
        try {
            $mailerLite = new MailerLite(['api_key' => $apiKey]);
    
            $response = $mailerLite->subscribers->get();

            ApiKey::updateOrCreate(['id' => 1], ['api_key' => $apiKey]);
    
            return redirect()->route('subscribers.index');
        } catch (MailerLiteHttpException $e) {
            return redirect()->back()->withErrors(['api_key' => 'Invalid API Key']);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['api_key' => 'Invalid API Key']);
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['api_key' => 'Invalid API Key']);
        }
    }

    /**
     * Display the subscribers table.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function showSubscribers()
    {
        $apiKey = ApiKey::first();

        if (!$apiKey) {
            return redirect()->route('api_key_form');
        }

        $mailerLite = $this->getMailerLiteInstance();
        if (!$mailerLite) {
            return response()->json(['message' => 'API key not found'], 400);
        }

        return view('subscribers.index');
    }

    /**
     * Display the form for creating a new subscriber.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('subscribers.create');
    }

    /**
     * Display the form for editing an existing subscriber.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function editSubscriberForm($id)
    {
        $mailerLite = $this->getMailerLiteInstance();

        $subscriber = $mailerLite->subscribers->find($id);

        return view('subscribers.edit',['subscriber' => $subscriber["body"]["data"]]);
    }

    /**
     * Create Subscriber using MailerLiteAPI
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function createSubscriber(Request $request)
    {
        $request->validate(
            [
            'email' => 'required|email',
            'name' => 'required',
            'country' => 'required',
            ]
        );

        $mailerLite = $this->getMailerLiteInstance();
        if (!$mailerLite) {
            return response()->json(['message' => 'API key not found'], 400);
        }

        //pass the correct data format
        $data = [
            'email' => $request->email,
            'fields' => [
                'name' => $request->name,
                'country' => $request->country,
            ],
        ];

        $response = $mailerLite->subscribers->create($data);

        $status = 'Subscriber created';

        if (isset($response->error)) {
            $status = $response->error->message;
        } else if ($response['status_code']==200) {
            $status = 'That subscriber email already exists';
        }

        return view('subscribers.create',['status' => $status]);
    }

    /**
     * Delete Subscriber using MailerLiteAPI
     *
     * @param int $id The ID of the subscriber to be deleted.
     * @return \Illuminate\Http\JsonResponse The JSON response of the operation.
     */
    public function deleteSubscriber($id)
    {
        $mailerLite = $this->getMailerLiteInstance();
        if (!$mailerLite) {
            return response()->json(['message' => 'API key not found'], 400);
        }

        $response = $mailerLite->subscribers->delete($id);

        if (isset($response->error)) {
            return response()->json(['message' => $response->error->message], 400);
        }

        return response()->json(['message' => 'Subscriber deleted']);
    }

    /**
     * Edit Subscriber using MailerLiteAPI
     *
     * @param int $id The ID of the subscriber to be deleted.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse The JSON response of the operation.
     */
    public function editSubscriber($id, Request $request)
    {
        $request->validate(
            [
            'name' => 'required',
            'country' => 'required',
            ]
        );

        $mailerLite = $this->getMailerLiteInstance();
        if (!$mailerLite) {
            return response()->json(['message' => 'API key not found'], 400);
        }

        //pass the correct data format
        $data = [
            'fields' => [
                'name' => $request->name,
                'country' => $request->country,
            ],
        ];

        $response = $mailerLite->subscribers->update($id, $data);

        if (isset($response->error)) {
            return response()->json(['message' => $response->error->message], 400);
        }

        return response()->json(['message' => 'Subscriber updated']);
    }

    /**
     * Retrieve subscriber data from MailerLite API.
     * 
     * @param Request $request
     * @return array|null
     */
    public function getSubscribersData(Request $request)
    {
        $mailerLite = $this->getMailerLiteInstance();
        if (!$mailerLite) {
            return null;
        }

        $options = [
            'limit' => 10,
            'cursor' => $request->cursor,
        ];        
    
        $subscribers = $mailerLite->subscribers->get($options);
        $totalSubscribers = count($subscribers['body']['data']);
    
        return [
            'data' => $subscribers['body']['data'],
            'recordsFiltered' => $totalSubscribers,
            'prevLink' => $subscribers['body']['meta']['prev_cursor'],
            'nextLink' => $subscribers['body']['meta']['next_cursor'],
        ];
    }
}
