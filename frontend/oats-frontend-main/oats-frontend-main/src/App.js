import React from 'react';

import './App.css';
import NavBar from './Common/Nav';
import SignInSide from './Common/SignInSide';
import ProductList from './Product/ProductList';
import ConversationList from './Conversation/ConversationList';


class App extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
      token: '',
      active: '',
      chatId: '',
      pusher: '',
      username : '',
      userId : '',
      points : 0,
      ban_period: null
    };
  }

  setToken = (token) => {
    this.setState({
      token: token
    });
  };

  setPusher = (pusher) => {
    this.setState({
      pusher: pusher
    });
  };

  setActive = (page, chatId) => {
    this.setState({
      active: page,
      chatId: chatId
    });
  };

  setNameAndBan = (name, userid, ban) =>{
    this.setState({
      username: name,
      userId: userid,
      ban_period: ban
    });
  };

  setPoints = (points) =>{
    this.setState({
      points: points
    });
  };

  renderContent() {
    if (this.state.active === "conversation") {
      return (
        <ConversationList token={this.state.token} setActive={this.setActive} chatId={this.state.chatId} pusher={this.state.pusher} userId = {this.state.userId} ban_period={this.state.ban_period}/>
      );
    } else if (this.state.active === "product") {
      return (
        <ProductList token={this.state.token} setActive={this.setActive}/>
      );
    }
  }

  render() {
    if (!this.state.token) {
      return (
        <SignInSide setToken={this.setToken} setActive={this.setActive} setPusher={this.setPusher} setNameAndBan={this.setNameAndBan} setPoints={this.setPoints}/>
      );
    }
    
    return (
      <div className="App">
        <header className="App-header">
          <NavBar setActive={this.setActive} username={this.state.username} token={this.state.token} caroupoints={this.state.points}/>
        </header>
        <div className="App-content">
          {this.renderContent()}
        </div>
      </div>
    );

  }
}

export default App;