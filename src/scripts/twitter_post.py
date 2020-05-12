#! /usr/bin/env python3
import tweepy, getopt, sys

# Twitter Application Consumer API Key and Access Token
consumer_key = 'xByg79nWgMQdxfzzfI9lBwkNt'
consumer_secret = 'CbQYdonxYjHoWiU683TbABYUjYtdsMh4sDMo8EKq21lCWFIuMM'
access_token = '1952832301-IzU35XWdZ5oYQO95t6ImvyYjbU2h9IdHiMT6L4L'
access_token_secret = '9o08UKCo9IFM7otOFaICKOqhKsoxp1Jo3FfETzHR3Tskc'

#Authenticate Twitter account
auth = tweepy.OAuthHandler(consumer_key, consumer_secret)
auth.set_access_token(access_token, access_token_secret)
api = tweepy.API(auth)

user = api.me()
user = api.get_user('@zeniea_')

api.update_status(sys.argv)

