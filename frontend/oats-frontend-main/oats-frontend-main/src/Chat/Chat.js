import React, { useState, useEffect } from 'react';
import { makeStyles } from '@material-ui/core/styles';
import axios from 'axios';
import Card from '@material-ui/core/Card';
import CardMedia from '@material-ui/core/CardMedia';
import { red } from '@material-ui/core/colors';
import Avatar from '@material-ui/core/Avatar';
import Typography from '@material-ui/core/Typography';

import ChatList from './ChatList';
import ChatBox from './ChatBox';
import './ChatUI.css';
import images from "./../Product/images";

const useStyles = makeStyles((theme) => ({
  root: {
    display: 'flex',
    padding: '5px 20px',
    marginBottom: 5,
    alignItems: "center",
  },

  media: {
    width: 40,
    height: 40,
    marginRight: 10
  },

  avatar: {
    backgroundColor: red[500],
    marginRight: 10,
  },
  
}));

export default function Chat(props) {
  const classes = useStyles();
  const [text, setText] = useState('');
  const [seller, setSeller] = useState('');
  const [chats, setChats] = useState([]);
  const [productId, setProductId] = useState();
  const [productName, setProductName] = useState('');
  const [productPrice, setProductPrice] = useState('');

  const handleTextChange = (e) => {
    if (e.keyCode === 13) {
      const payload = {
        message: text
      };

      //Send to BE for BROADCAST
      axios.post(process.env.REACT_APP_BE_URL + 'messageAsync/' + props.chatId, payload, {
        headers: {
          'Authorization': `Bearer ${props.token}` ,          
          'X-Socket-ID' : props.pusher.connection.socket_id,
        }
      }).then(function(response){
        setText('')
        setChats(chats=>[...chats, response.data.data]);
      });

      //Send to Lambda for analysis
      axios.put(process.env.REACT_APP_SENTIMENT_URL, payload, {
        headers : {
          'Content-Type': 'application/json',
        }
      }).then(function(response){
        if (response.data.body.Sentiment === "NEGATIVE") {
          alert('Negative Message Detected. Please refrain from such negative remarks. Caroupoint will be deducted.');
        }
      });

    } else {
      setText(e.target.value);
    }
  }

  const handleOfferBtn = (id,status) => {
    console.log(id);
    axios.post(process.env.REACT_APP_BE_URL + 'message/sellerUpdate/' + id + '/' + status, null ,{
      headers: {
        'Authorization': `Bearer ${props.token}` 
      }
    }).then(function(response){
      if(response.data.data.length){
        setChats([...response.data.data]);
      }
    });
  }

  useEffect(() => {
    //Connect and subscribe to PUSHER API
    const channel = props.pusher.subscribe('chat'+props.chatId);
    channel.bind('MessageSent', data => {
      setChats(chats=>[...chats, data]);
    });

    //Connect to user individual channel
    channel.bind('UpdateChat', data => {
      axios.get(process.env.REACT_APP_BE_URL + 'message/' + props.chatId, {
        headers: {
          'Authorization': `Bearer ${props.token}` 
        }
      }).then(function(response){
        if(response.data.data.messages.length){
          setChats([...response.data.data.messages]);          
        }
      });
    });
    //Get all chat history
    axios.get(process.env.REACT_APP_BE_URL + 'message/' + props.chatId, {
      headers: {
        'Authorization': `Bearer ${props.token}` 
      }
    }).then(function(response){
        setProductId(response.data.data.listing_id);
        setSeller(response.data.data.listing_user);
        setProductName(response.data.data.listing_item);
        setProductPrice(response.data.data.listing_price);
      if(response.data.data.messages.length){
        setChats([...response.data.data.messages]);          
      }
    });

    return () => {
      //Unsubscribe to PUSHER API channel
      props.pusher.unsubscribe('chat'+props.chatId);
    }
  }, []);


  return (
    <div className="ChatUI">
      <div className="ChatTitle">
        <Card  className={classes.root}>
          <Avatar aria-label="User" className={classes.avatar}>
            {seller.substring(0, 1)}
          </Avatar>
          <Typography variant="subtitle1" color="textPrimary">
            {seller}
          </Typography>
        </Card>
        <Card  className={classes.root}>
          <CardMedia
            className={classes.media}
            image={images["p"+productId]}
            title={productName}
          />
          <Typography variant="subtitle1" color="textPrimary">
            {productName} <b>${productPrice}</b>
          </Typography>
        </Card>
      </div>
      <div className="ChatList">
        <ChatList chats={chats} handleOfferBtn={handleOfferBtn}/>
      </div>
      <div className="ChatBox">
        <ChatBox
          text={text}
          ban_period={props.ban_period}
          handleTextChange={handleTextChange}
        />
      </div>
    </div>
  );
}
