<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use MailerLite\MailerLite;

class MailerLiteController extends Controller
{
    public function showApiKeyForm()
    {
        $apiKey = ApiKey::first();

        if (!$apiKey) {
            return view('api_key_form',['api_key' => ""]);
        }

        return view('api_key_form',['api_key' => $apiKey->api_key]);
    }

    private function getMailerLiteInstance()
    {
        $apiKey = ApiKey::first();
        if (!$apiKey) {
            return null;
        }

        return new MailerLite(['api_key' => $apiKey->api_key]);
    }

    public function validateAndSaveApiKey(Request $request)
    {
        $request->validate([
            'api_key' => 'required',
        ]);
    
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

    public function create()
    {
        return view('subscribers.create');
    }

    public function editSubscriberForm($id)
    {
        $mailerLite = $this->getMailerLiteInstance();

        $subscriber = $mailerLite->subscribers->find($id);

        return view('subscribers.edit',['subscriber' => $subscriber["body"]["data"]]);
    }

    public function createSubscriber(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'country' => 'required',
        ]);

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
        }
        else if($response['status_code']==200){
            $status = 'That subscriber email already exists';
        }

        return view('subscribers.create',['status' => $status]);
    }

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

    public function editSubscriber($id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'country' => 'required',
        ]);

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
